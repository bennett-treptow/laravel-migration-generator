<?php

namespace LaravelMigrationGenerator\Tokenizers;

use LaravelMigrationGenerator\Definitions\IndexDefinition;
use LaravelMigrationGenerator\Tokenizers\Interfaces\IndexTokenizerInterface;
use LaravelMigrationGenerator\Tokenizers\Interfaces\ColumnTokenizerInterface;

abstract class BaseIndexTokenizer extends BaseTokenizer implements IndexTokenizerInterface
{
    protected IndexDefinition $definition;

    protected array $relatedColumns = [];

    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->definition = new IndexDefinition();
    }

    public function column(ColumnTokenizerInterface $column)
    {
        $this->relatedColumns[] = $column;

        return $this;
    }

    public function definition(): IndexDefinition
    {
        return $this->definition;
    }
}
