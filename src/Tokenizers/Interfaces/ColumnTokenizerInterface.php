<?php

namespace LaravelMigrationGenerator\Tokenizers\Interfaces;

use LaravelMigrationGenerator\Definitions\ColumnDefinition;

interface ColumnTokenizerInterface
{
    public function tokenize(): self;

    public function definition(): ColumnDefinition;
}
