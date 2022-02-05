<?php

namespace LaravelMigrationGenerator\Generators\Interfaces;

use LaravelMigrationGenerator\Definitions\TableDefinition;

interface TableGeneratorInterface
{
    public static function driver(): string;

    public function shouldResolveStructure(): bool;

    public function resolveStructure();

    public function parse();

    public function cleanUp();

    public function definition(): TableDefinition;
}
