<?php

namespace LaravelMigrationGenerator\GeneratorManagers;

use LaravelMigrationGenerator\Helpers\DependencyResolver;
use LaravelMigrationGenerator\Definitions\TableDefinition;
use LaravelMigrationGenerator\Definitions\DependentTableDefinition;

abstract class BaseGeneratorManager
{
    public function createMissingDirectory($basePath)
    {
        if (! is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }
    }

    public function sortTables(array $tableDefinitions)
    {
        $finalOrder = [];
        if (config('laravel-migration-generator.sort_mode') == 'foreign_key') {
            $keyedTableDefinitions = collect($tableDefinitions)->keyBy(function ($tableDefinition) {
                return $tableDefinition->getTableName();
            })->toArray();
            $resolver = new DependencyResolver($tableDefinitions);
            [$nonCirculars, $circulars] = $resolver->getDependencyOrder();
            foreach ($nonCirculars as $nonCircular) {
                $finalOrder[] = $keyedTableDefinitions[$nonCircular];
            }
            foreach ($circulars as $circular) {
                $finalOrder[] = new DependentTableDefinition(array_map(function ($piece) use ($keyedTableDefinitions) {
                    return $keyedTableDefinitions[$piece];
                }, $circular));
            }
        } else {
            $finalOrder = $tableDefinitions;
        }

        return $finalOrder;
    }

    public function writeTableMigrations(array $tableDefinitions, $basePath)
    {
        foreach ($tableDefinitions as $tableDefinition) {
            /** @var TableDefinition|DependenttableDefinition $tableDefinition */
            $tableDefinition->write($basePath);
        }
    }
}
