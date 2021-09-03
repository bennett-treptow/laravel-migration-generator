<?php

namespace LaravelMigrationGenerator\GeneratorManagers\Interfaces;

use Illuminate\Console\OutputStyle;
use LaravelMigrationGenerator\Definitions\TableDefinition;
use LaravelMigrationGenerator\Generators\BaseViewGenerator;

interface GeneratorManagerInterface
{
    public static function driver(): string;

    public function handle(string $basePath, array $tableNames = [], array $viewNames = []);

    public function addTableDefinition(TableDefinition $definition);

    public function addViewDefinition(BaseViewGenerator $generator);

    public function getTableDefinitions(): array;

    public function getViewDefinitions(): array;

    public function getOutputBuffer(): array;
}
