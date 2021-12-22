<?php

namespace BennettTreptow\LaravelMigrationGenerator\Tokenizers;

use BennettTreptow\LaravelMigrationGenerator\Definitions\IndexDefinition;
use BennettTreptow\LaravelMigrationGenerator\Tokenizers\Interfaces\IndexTokenizerInterface;

abstract class BaseIndexTokenizer extends BaseTokenizer implements IndexTokenizerInterface
{
    protected IndexDefinition $definition;

    public function __construct(string $value)
    {
        $this->definition = new IndexDefinition();
        parent::__construct($value);
    }

    public function definition(): IndexDefinition
    {
        return $this->definition;
    }
}
