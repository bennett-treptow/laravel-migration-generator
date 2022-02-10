<?php

namespace BennettTreptow\LaravelMigrationGenerator\Tokenizers\Interfaces;

use BennettTreptow\LaravelMigrationGenerator\Definitions\IndexDefinition;

interface IndexTokenizerInterface
{
    public function tokenize(): self;

    public function definition(): IndexDefinition;
}
