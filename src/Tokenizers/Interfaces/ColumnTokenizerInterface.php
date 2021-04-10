<?php

namespace LaravelMigrationGenerator\Tokenizers\Interfaces;

use LaravelMigrationGenerator\Definitions\ColumnDefinition;
use LaravelMigrationGenerator\Generators\Interfaces\TableGeneratorInterface;

interface ColumnTokenizerInterface
{
    public function tokenize(): self;

    public function finalPass(TableGeneratorInterface $tableGenerator): ?bool;

    public function definition(): ColumnDefinition;
}
