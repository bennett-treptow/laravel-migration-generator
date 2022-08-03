<?php

namespace LaravelMigrationGenerator\Tokenizers\MySQL;

use Illuminate\Support\Str;
use Illuminate\Database\Schema\Builder;
use LaravelMigrationGenerator\Helpers\ValueToString;
use LaravelMigrationGenerator\Tokenizers\BaseColumnTokenizer;

class ColumnTokenizer extends BaseColumnTokenizer
{
    protected $columnDataType;

    /**
     * MySQL provides a ZEROFILL property for ints which is not an ANSI compliant modifier
     * @var bool
     */
    protected $zeroFill = false;

    public function tokenize(): self
    {
        $this->consumeColumnName();
        $this->consumeColumnType();
        if ($this->isNumberType()) {
            $this->consumeUnsigned();
            $this->consumeZeroFill();
        }
        if ($this->isTextType()) {
            //possibly has a character set
            $this->consumeCharacterSet();

            //has collation data most likely
            $this->consumeCollation();
        }

        $this->consumeNullable();

        $this->consumeDefaultValue();
        if ($this->isNumberType()) {
            $this->consumeAutoIncrement();
            $this->consumeKeyConstraints();
        }

        $this->consumeGenerated();

        if ($this->columnDataType == 'timestamp' || $this->columnDataType == 'datetime') {
            $this->consumeTimestamp();
        }

        $this->consumeComment();

        return $this;
    }

    //region Consumers

    protected function consumeColumnName()
    {
        $this->definition->setColumnName($this->parseColumn($this->consume()));
    }

    protected function consumeZeroFill()
    {
        $nextPiece = $this->consume();

        if (strtoupper($nextPiece) === 'ZEROFILL') {
            $this->zeroFill = true;
        } else {
            $this->putBack($nextPiece);
        }
    }

    protected function consumeColumnType()
    {
        $originalColumnType = $columnType = strtolower($this->consume());
        $hasConstraints = Str::contains($columnType, '(');

        if ($hasConstraints) {
            $columnType = explode('(', $columnType)[0];
        }

        $this->columnDataType = $columnType;

        $this->resolveColumnMethod();
        if ($hasConstraints) {
            preg_match("/\((.+?)\)/", $originalColumnType, $constraintMatches);
            $matches = explode(',', $constraintMatches[1]);
            $this->resolveColumnConstraints($matches);
        }
    }

    private function consumeAutoIncrement()
    {
        $piece = $this->consume();
        if (strtoupper($piece) === 'AUTO_INCREMENT') {
            $this->definition->setAutoIncrementing(true);
        } else {
            $this->putBack($piece);
        }
    }

    protected function consumeNullable()
    {
        $piece = $this->consume();
        if (strtoupper($piece) === 'NOT') {
            $this->consume(); //next is NULL
            $this->definition->setNullable(false);
        } elseif (strtoupper($piece) === 'NULL') {
            $this->definition->setNullable(true);
        } else {
            //something else
            $this->putBack($piece);
        }

        if (Str::contains($this->columnDataType, 'text')) {
            //text column types are explicitly nullable unless set to NOT NULL
            if ($this->definition->isNullable() === null) {
                $this->definition->setNullable(true);
            }
        }
    }

    protected function consumeDefaultValue()
    {
        $piece = $this->consume();
        if (strtoupper($piece) === 'DEFAULT') {
            $this->definition->setDefaultValue($this->consume());

            if (strtoupper($this->definition->getDefaultValue()) === 'NULL') {
                $this->definition
                    ->setNullable(true)
                    ->setDefaultValue(null);
            } elseif (strtoupper($this->definition->getDefaultValue()) === 'CURRENT_TIMESTAMP') {
                $this->definition
                    ->setDefaultValue(null)
                    ->setUseCurrent(true);
            } elseif (preg_match("/b'([01]+)'/i", $this->definition->getDefaultValue(), $matches)) {
                // Binary digit, so let's convert to PHP's version
                $this->definition->setDefaultValue(ValueToString::castBinary($matches[1]));
            }
            if ($this->definition->getDefaultValue() !== null) {
                if ($this->isNumberType()) {
                    if (Str::contains(strtoupper($this->columnDataType), 'INT')) {
                        $this->definition->setDefaultValue((int) $this->definition->getDefaultValue());
                    } else {
                        //floats get converted to strings improperly, gotta do a string cast
                        $this->definition->setDefaultValue(ValueToString::castFloat($this->definition->getDefaultValue()));
                    }
                } else {
                    if (! $this->isBinaryType()) {
                        $this->definition->setDefaultValue((string) $this->definition->getDefaultValue());
                    }
                }
            }
        } else {
            $this->putBack($piece);
        }
    }

    protected function consumeComment()
    {
        $piece = $this->consume();
        if (strtoupper($piece) === 'COMMENT') {
            // next piece is the comment content
            $this->definition->setComment($this->consume());
        } else {
            $this->putBack($piece);
        }
    }

    protected function consumeCharacterSet()
    {
        $piece = $this->consume();

        if (strtoupper($piece) === 'CHARACTER') {
            $this->consume(); // SET, throw it away

            $this->definition->setCharacterSet($this->consume());
        } else {
            //something else
            $this->putBack($piece);
        }
    }

    protected function consumeCollation()
    {
        $piece = $this->consume();
        if (strtoupper($piece) === 'COLLATE') {
            //next piece is the collation type
            $this->definition->setCollation($this->consume());
        } else {
            $this->putBack($piece);
        }
    }

    private function consumeUnsigned()
    {
        $piece = $this->consume();
        if (strtoupper($piece) === 'UNSIGNED') {
            $this->definition->setUnsigned(true);
        } else {
            $this->putBack($piece);
        }
    }

    private function consumeKeyConstraints()
    {
        $nextPiece = $this->consume();
        if (strtoupper($nextPiece) === 'PRIMARY') {
            $this->definition->setPrimary(true);

            $next = $this->consume();
            if (strtoupper($next) !== 'KEY') {
                $this->putBack($next);
            }
        } elseif (strtoupper($nextPiece) === 'UNIQUE') {
            $this->definition->setUnique(true);

            $next = $this->consume();
            if (strtoupper($next) !== 'KEY') {
                $this->putBack($next);
            }
        } else {
            $this->putBack($nextPiece);
        }
    }

    private function consumeGenerated()
    {
        $canContinue = false;
        $nextPiece = $this->consume();
        if (strtoupper($nextPiece) === 'GENERATED') {
            $piece = $this->consume();
            if (strtoupper($piece) === 'ALWAYS') {
                $this->consume(); // AS
                $canContinue = true;
            } else {
                $this->putBack($piece);
            }
        } elseif (strtoupper($nextPiece) === 'AS') {
            $canContinue = true;
        }

        if (! $canContinue) {
            $this->putBack($nextPiece);

            return;
        }

        $expressionPieces = [];
        $parenthesisCounter = 0;
        while ($pieceOfExpression = $this->consume()) {
            $numOpeningParenthesis = substr_count($pieceOfExpression, '(');
            $numClosingParenthesis = substr_count($pieceOfExpression, ')');
            $parenthesisCounter += $numOpeningParenthesis - $numClosingParenthesis;

            $expressionPieces[] = $pieceOfExpression;

            if ($parenthesisCounter === 0) {
                break;
            }
        }
        $expression = implode(' ', $expressionPieces);
        if (Str::startsWith($expression, '((') && Str::endsWith($expression, '))')) {
            $expression = substr($expression, 1, strlen($expression) - 2);
        }

        $finalPiece = $this->consume();
        if ($finalPiece !== null && strtoupper($finalPiece) === 'STORED') {
            $this->definition->setStoredAs($expression)->setNullable(false);
        } else {
            $this->definition->setVirtualAs($expression)->setNullable(false);
        }
    }

    private function consumeTimestamp()
    {
        $nextPiece = $this->consume();
        if (strtoupper($nextPiece) === 'ON') {
            $next = $this->consume();
            if (strtoupper($next) === 'UPDATE') {
                $next = $this->consume();
                if (strtoupper($next) === 'CURRENT_TIMESTAMP') {
                    $this->definition->setUseCurrentOnUpdate(true);
                } else {
                    $this->putBack($next);
                }
            } else {
                $this->putBack($next);
            }
        } else {
            $this->putBack($nextPiece);
        }
    }

    //endregion

    //region Resolvers
    private function resolveColumnMethod()
    {
        $mapped = [
            'int'                => 'integer',
            'tinyint'            => 'tinyInteger',
            'smallint'           => 'smallInteger',
            'mediumint'          => 'mediumInteger',
            'bigint'             => 'bigInteger',
            'varchar'            => 'string',
            'tinytext'           => 'string',  //tinytext is not a valid Blueprint method currently
            'mediumtext'         => 'mediumText',
            'longtext'           => 'longText',
            'blob'               => 'binary',
            'datetime'           => 'dateTime',
            'geometrycollection' => 'geometryCollection',
            'linestring'         => 'lineString',
            'multilinestring'    => 'multiLineString',
            'multipolygon'       => 'multiPolygon',
            'multipoint'         => 'multiPoint'
        ];
        if (isset($mapped[$this->columnDataType])) {
            $this->definition->setMethodName($mapped[$this->columnDataType]);
        } else {
            //do some custom resolution
            $this->definition->setMethodName($this->columnDataType);
        }
    }

    private function resolveColumnConstraints(array $constraints)
    {
        if ($this->columnDataType === 'char' && count($constraints) === 1 && $constraints[0] == 36) {
            //uuid for mysql
            $this->definition->setIsUUID(true);

            return;
        }
        if ($this->isArrayType()) {
            $this->definition->setMethodParameters([array_map(fn ($item) => trim($item, '\''), $constraints)]);
        } else {
            if (Str::contains(strtoupper($this->columnDataType), 'INT')) {
                $this->definition->setMethodParameters([]); //laravel does not like display field widths
            } else {
                if ($this->definition->getMethodName() === 'string') {
                    if (count($constraints) === 1) {
                        //has a width set
                        if ($constraints[0] == Builder::$defaultStringLength) {
                            $this->definition->setMethodParameters([]);

                            return;
                        }
                    }
                }
                $this->definition->setMethodParameters(array_map(fn ($item) => (int) $item, $constraints));
            }
        }
    }

    //endregion

    protected function isTextType()
    {
        return Str::contains($this->columnDataType, ['char', 'text', 'set', 'enum']);
    }

    protected function isNumberType()
    {
        return Str::contains($this->columnDataType, ['int', 'decimal', 'float', 'double']);
    }

    protected function isArrayType()
    {
        return Str::contains($this->columnDataType, ['enum', 'set']);
    }

    protected function isBinaryType()
    {
        return Str::contains($this->columnDataType, ['bit']);
    }

    /**
     * @return mixed
     */
    public function getColumnDataType()
    {
        return $this->columnDataType;
    }
}
