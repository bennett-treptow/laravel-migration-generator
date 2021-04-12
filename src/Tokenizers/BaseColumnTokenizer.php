<?php

namespace LaravelMigrationGenerator\Tokenizers;

use LaravelMigrationGenerator\Definitions\ColumnDefinition;
use LaravelMigrationGenerator\Tokenizers\Interfaces\ColumnTokenizerInterface;

abstract class BaseColumnTokenizer extends BaseTokenizer implements ColumnTokenizerInterface
{
    protected ColumnDefinition $definition;

    public function __construct(string $value)
    {
        $this->definition = new ColumnDefinition();
        parent::__construct($value);
    }

    public function definition(): ColumnDefinition
    {
        return $this->definition;
    }
}
