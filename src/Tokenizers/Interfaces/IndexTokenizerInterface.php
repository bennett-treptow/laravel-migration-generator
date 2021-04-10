<?php

namespace LaravelMigrationGenerator\Tokenizers\Interfaces;

use LaravelMigrationGenerator\Definitions\IndexDefinition;
use LaravelMigrationGenerator\Generators\Interfaces\TableGeneratorInterface;

interface IndexTokenizerInterface
{
    public function tokenize(): self;

    public function finalPass(TableGeneratorInterface $table): ?bool;

    public function definition(): IndexDefinition;
}
