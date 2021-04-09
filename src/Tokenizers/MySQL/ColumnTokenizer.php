<?php

namespace LaravelMigrationGenerator\Tokenizers\MySQL;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Tokenizers\WritableTokenizer;
use LaravelMigrationGenerator\Tokenizers\BaseColumnTokenizer;
use LaravelMigrationGenerator\Generators\TableGeneratorInterface;

class ColumnTokenizer extends BaseColumnTokenizer
{
    use WritableTokenizer;

    /** @var IndexTokenizer[] */
    protected $indices = [];

    protected $indexed = false;

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
                if ($index->getIndexType() === 'primary' && ! $index->isMultiColumnIndex()) {
                    $this->primaryKey = true;
                    $index->markAsWritable(false);
                } elseif ($index->getIndexType() === 'index' && ! $index->isMultiColumnIndex()) {
                    $this->indexed = true;
                    $index->markAsWritable(false);
                }
            }
        }

        if ($this->columnName === 'created_at' && $this->columnType === 'timestamp') {
            //let's look for an updated_at
            $table->columnIterator(function ($column) {
                if ($column->getColumnName() === 'updated_at' && $column->getColumnType() === 'timestamp') {
                    $column->markAsWritable(false);

                    $this->method = 'timestamps';
                    $this->columnName = null;
                    $this->nullable = false;

                    return false;
                }
            });
        }

        if ($this->columnName === 'id' && $this->primaryKey && $this->columnType === 'bigint') {
            $this->columnName = null;
        }

        return null;
    }

    protected function consumeColumnName()
    {
        $this->columnName = $this->parseColumn($this->consume());
    }

    protected function isTextType()
    {
        return Str::contains($this->columnType, ['char', 'text']);
    }

    protected function isNumberType()
    {
        return Str::contains($this->columnType, ['int', 'decimal', 'float', 'double']);
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
            $this->primaryKey = true;
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
            $this->method = $mapped[$this->columnType];
        } else {
            //do some custom resolution
            $this->method = $this->columnType;
        }
    }

    protected function consumeNullable()
    {
        $piece = $this->consume();
        if (strtoupper($piece) === 'NOT') {
            $this->consume(); //next is NULL
            $this->nullable = false;
        } elseif (strtoupper($piece) === 'NULL') {
            $this->nullable = true;
        } else {
            //something else
            $this->putBack($piece);
        }
    }

    protected function consumeDefaultValue()
    {
        $piece = $this->consume();
        if (strtoupper($piece) === 'DEFAULT') {
            $this->defaultValue = $this->consume();
            if (strtoupper($this->defaultValue) === 'NULL') {
                $this->nullable = true;
                $this->defaultValue = null;
            } elseif (strtoupper($this->defaultValue === 'CURRENT_TIMESTAMP')) {
                $this->defaultValue = null;
                $this->useCurrent = true;
            }
            if ($this->isNumberType()) {
                if (Str::contains(strtoupper($this->columnType), 'INT')) {
                    $this->defaultValue = (int) $this->defaultValue;
                } else {
                    $this->defaultValue = 'float$:' . $this->defaultValue;
                }
            } else {
                if ($this->defaultValue !== null) {
                    $this->defaultValue = (string) $this->defaultValue;
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
            $this->collation = $this->consume();
        } else {
            $this->putBack($piece);
        }
    }

    private function consumeUnsigned()
    {
        $piece = $this->consume();
        if (strtoupper($piece) === 'UNSIGNED') {
            $this->unsigned = true;
        } else {
            $this->putBack($piece);
        }
    }

    private function resolveColumnConstraints(array $constraints)
    {
        if ($this->columnType === 'enum') {
            $this->methodParameters = [array_map(fn ($item) => trim($item, '\''), $constraints)];
        } else {
            $this->methodParameters = array_map(fn ($item) => (int) $item, $constraints);
        }
    }

    public function toMethod(): string
    {
        $initialString = parent::toMethod();
        if ($this->indexed) {
            $initialString .= '->index()';
        }

        return $initialString;
    }
}
