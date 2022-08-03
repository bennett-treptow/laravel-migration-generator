<?php

namespace LaravelMigrationGenerator\GeneratorManagers\Interfaces;

use LaravelMigrationGenerator\Definitions\ViewDefinition;
use LaravelMigrationGenerator\Definitions\TableDefinition;

interface GeneratorManagerInterface
{
    public static function driver(): string;

    public function handle(string $basePath, array $tableNames = [], array $viewNames = []);

    public function addTableDefinition(TableDefinition $definition);

    public function addViewDefinition(ViewDefinition $definition);

    public function getTableDefinitions(): array;

    public function getViewDefinitions(): array;
}
