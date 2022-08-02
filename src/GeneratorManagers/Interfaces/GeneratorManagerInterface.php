<?php

namespace BennettTreptow\LaravelMigrationGenerator\GeneratorManagers\Interfaces;

use BennettTreptow\LaravelMigrationGenerator\Definitions\TableDefinition;
use BennettTreptow\LaravelMigrationGenerator\Definitions\ViewDefinition;

interface GeneratorManagerInterface
{
    public static function driver(): string;

    public function handle(string $basePath, array $tableNames = [], array $viewNames = []);

    public function addTableDefinition(TableDefinition $definition);

    public function addViewDefinition(ViewDefinition $definition);

    public function getTableDefinitions(): array;

    public function getViewDefinitions(): array;
}
