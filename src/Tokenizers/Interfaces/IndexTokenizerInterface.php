<?php

namespace LaravelMigrationGenerator\Tokenizers\Interfaces;

use LaravelMigrationGenerator\Definitions\IndexDefinition;

interface IndexTokenizerInterface
{
    public function tokenize(): self;

    public function definition(): IndexDefinition;
}
