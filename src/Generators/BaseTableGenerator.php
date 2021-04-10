<?php

namespace LaravelMigrationGenerator\Generators;

use LaravelMigrationGenerator\Tokenizers\Traits\WritableTokenizer;
use LaravelMigrationGenerator\Generators\Interfaces\TableGeneratorInterface;
use LaravelMigrationGenerator\Tokenizers\Interfaces\IndexTokenizerInterface;
use LaravelMigrationGenerator\Tokenizers\Interfaces\ColumnTokenizerInterface;

abstract class BaseTableGenerator implements TableGeneratorInterface
{
    use WritableTokenizer;

    protected $tableName;

    protected $rows;

    /** @var ColumnTokenizerInterface[] */
    protected $columns = [];

    /** @var IndexTokenizerInterface[] */
    protected $indices = [];

    public static function init(string $tableName)
    {
        $instance = (new static($tableName));

        if ($instance->shouldResolveStructure()) {
            $instance->resolveStructure();
        }

        $instance->parse();
        $instance->cleanUp();

        return $instance;
    }

    public function shouldResolveStructure(): bool
    {
        return count($this->rows) === 0;
    }

    public function columnIterator(callable $callback)
    {
        foreach ($this->columns as &$column) {
            $returned = $callback($column);
            if ($returned === false) {
                break;
            }
        }
    }

    public function indexIterator(callable $callback)
    {
        foreach ($this->indices as &$column) {
            $returned = $callback($column);
            if ($returned === false) {
                break;
            }
        }
    }

    public function getIndices()
    {
        return $this->indices;
    }
}
