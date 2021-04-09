<?php

namespace LaravelMigrationGenerator\Generators;

use LaravelMigrationGenerator\Tokenizers\WritableTokenizer;
use LaravelMigrationGenerator\Tokenizers\IndexTokenizerInterface;
use LaravelMigrationGenerator\Tokenizers\ColumnTokenizerInterface;

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

        $instance->parse();
        $instance->finalPass();

        return $instance;
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
}
