<?php

namespace LaravelMigrationGenerator\Tokenizers\MySQL;

use Illuminate\Support\Str;
use Illuminate\Database\Schema\Builder;
use LaravelMigrationGenerator\Definitions\ColumnDefinition;
use LaravelMigrationGenerator\Helpers\ValueToString;
use LaravelMigrationGenerator\Tokenizers\BaseColumnTokenizer;
use LaravelMigrationGenerator\Tokenizers\Traits\WritableTokenizer;
use LaravelMigrationGenerator\Generators\Interfaces\TableGeneratorInterface;

class ColumnTokenizer extends BaseColumnTokenizer
{
    use WritableTokenizer;

    protected $columnType;

    /** @var IndexTokenizer[] */
    protected $indices = [];

    /**
     * MySQL provides a ZEROFILL property for ints which is not an ANSI compliant modifier
     * @var bool
     */
    protected $zeroFill = false;

    public function index(IndexTokenizer $index)
    {
        $this->indices[] = $index;

        return $this;
    }

    public function tokenize(): self
    {
        $this->consumeColumnName();
        $this->consumeColumnType();
        if ($this->isNumberType()) {
            $this->consumeUnsigned();
            $this->consumeZeroFill();
        }
        if ($this->isTextType()) {
            //has collation data most likely
            $this->consumeCollation();
        }

        $this->consumeNullable();

        $this->consumeDefaultValue();
        if ($this->isNumberType()) {
            $this->consumeAutoIncrement();
        }

        return $this;
    }

    public function finalPass(TableGeneratorInterface $table): ?bool
    {
        if (count($this->indices) > 0) {
            foreach ($this->indices as $index) {
                if ($index->definition()->getIndexType() === 'primary' && ! $index->definition()->isMultiColumnIndex()) {
                    $this->definition->setPrimary(true);
                    $index->markAsWritable(false);
                } elseif ($index->definition()->getIndexType() === 'index' && ! $index->definition()->isMultiColumnIndex()) {
                    $this->definition->setIndex(true);
                    $index->markAsWritable(false);
                } elseif ($index->definition()->getIndexType() === 'unique' && ! $index->definition()->isMultiColumnIndex()) {
                    $this->definition->setUnique(true);
                    $index->markAsWritable(false);
                }
            }
        }

        return null;
    }

    protected function consumeColumnName()
    {
        $this->definition->setColumnName($this->parseColumn($this->consume()));
    }

    protected function isTextType()
    {
        return Str::contains($this->columnType, ['char', 'text']);
    }

    protected function isNumberType()
    {
        return Str::contains($this->columnType, ['int', 'decimal', 'float', 'double']);
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
        $originalColumnType = $columnType = $this->consume();
        $hasConstraints = Str::contains($columnType, '(');

        if ($hasConstraints) {
            $columnType = explode('(', $columnType)[0];
        }

        $this->columnType = $columnType;

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
            $this->definition->setPrimary(true);
        } else {
            $this->putBack($piece);
        }
    }

    private function resolveColumnMethod()
    {
        $mapped = [
            'int'        => 'integer',
            'tinyint'    => 'tinyInteger',
            'smallint'   => 'smallInteger',
            'mediumint'  => 'mediumInteger',
            'bigint'     => 'bigInteger',
            'varchar'    => 'string',
            'tinytext'   => 'tinyText',
            'mediumtext' => 'mediumText',
            'longtext'   => 'longText',
            'blob'       => 'binary',
            'datetime'   => 'dateTime'
        ];
        if (isset($mapped[$this->columnType])) {
            $this->definition->setMethodName($mapped[$this->columnType]);
        } else {
            //do some custom resolution
            $this->definition->setMethodName($this->columnType);
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
            }
            if ($this->definition->getDefaultValue() !== null) {
                if ($this->isNumberType()) {
                    if (Str::contains(strtoupper($this->columnType), 'INT')) {
                        $this->definition->setDefaultValue((int) $this->definition->getDefaultValue());
                    } else {
                        $this->definition->setDefaultValue(ValueToString::castFloat($this->definition->getDefaultValue()));
                    }
                } else {
                    $this->definition->setDefaultValue((string) $this->definition->getDefaultValue());
                }
            }
        } else {
            //put it back
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

    private function resolveColumnConstraints(array $constraints)
    {
        if ($this->columnType === 'enum') {
            $this->definition->setMethodParameters([array_map(fn ($item) => trim($item, '\''), $constraints)]);
        } else {
            if (Str::contains(strtoupper($this->columnType), 'INT')) {
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

    public function definition(): ColumnDefinition
    {
        return $this->definition;
    }

    /**
     * @return mixed
     */
    public function getColumnType()
    {
        return $this->columnType;
    }
}
