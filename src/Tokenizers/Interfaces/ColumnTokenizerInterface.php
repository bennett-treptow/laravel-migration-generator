<?php

namespace BennettTreptow\LaravelMigrationGenerator\Tokenizers\Interfaces;

use BennettTreptow\LaravelMigrationGenerator\Definitions\ColumnDefinition;

interface ColumnTokenizerInterface
{
    public function tokenize(): self;

    public function definition(): ColumnDefinition;
}
