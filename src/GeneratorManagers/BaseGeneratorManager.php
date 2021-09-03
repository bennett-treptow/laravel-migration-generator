<?php

namespace LaravelMigrationGenerator\GeneratorManagers;

use LaravelMigrationGenerator\Helpers\ConfigResolver;
use LaravelMigrationGenerator\Helpers\DependencyResolver;
use LaravelMigrationGenerator\Definitions\IndexDefinition;
use LaravelMigrationGenerator\Definitions\TableDefinition;
use LaravelMigrationGenerator\Generators\BaseViewGenerator;
use LaravelMigrationGenerator\GeneratorManagers\Interfaces\GeneratorManagerInterface;

abstract class BaseGeneratorManager implements GeneratorManagerInterface
{
    protected array $tableDefinitions = [];

    protected array $viewDefinitions = [];


    abstract public function init();

    public function createMissingDirectory($basePath)
    {
        if (! is_dir($basePath)) {
            mkdir($basePath, 0777, true);
        }
    }

    /**
     * @return array<TableDefinition>
     */
    public function getTableDefinitions(): array
    {
        return $this->tableDefinitions;
    }

    /**
     * @return array<BaseViewGenerator>
     */
    public function getViewDefinitions(): array
    {
        return $this->viewDefinitions;
    }

    public function addTableDefinition(TableDefinition $tableDefinition): BaseGeneratorManager
    {
        $this->tableDefinitions[] = $tableDefinition;

        return $this;
    }

    public function addViewDefinition(BaseViewGenerator $generator): BaseGeneratorManager
    {
        $this->viewDefinitions[] = $generator;

        return $this;
    }

    public function handle(string $basePath, array $tableNames = [], array $viewNames = [])
    {
        $this->init();

        $tableDefinitions = collect($this->getTableDefinitions());
        $viewDefinitions = collect($this->getViewDefinitions());

        $this->createMissingDirectory($basePath);

        if (count($tableNames) > 0) {
            $tableDefinitions = $tableDefinitions->filter(function ($tableDefinition) use ($tableNames) {
                return in_array($tableDefinition->getTableName(), $tableNames);
            })->toArray();
        }
        if (count($viewNames) > 0) {
            $viewDefinitions = $viewDefinitions->filter(function ($viewGenerator) use ($viewNames) {
                return in_array($viewGenerator->getViewName(), $viewNames);
            })->toArray();
        }

        $tableDefinitions = $tableDefinitions->filter(function ($tableDefinition) {
            return ! $this->skipTable($tableDefinition->getTableName());
        });

        $viewDefinitions = $viewDefinitions->filter(function ($viewDefinition) {
            return ! $this->skipView($viewDefinition->getViewName());
        });

        $sorted = $this->sortTables($tableDefinitions->toArray());

        $this->writeTableMigrations($sorted, $basePath);

        $this->writeViewMigrations($viewDefinitions->toArray(), $basePath);
    }

    /**
     * @param array<TableDefinition> $tableDefinitions
     * @return array<TableDefinition>
     */
    public function sortTables(array $tableDefinitions): array
    {
        if (count($tableDefinitions) <= 1) {
            return $tableDefinitions;
        }

        if (config('laravel-migration-generator.sort_mode') == 'foreign_key') {
            $finalOrder = [];

            $keyedTableDefinitions = collect($tableDefinitions)->keyBy(function ($tableDefinition) {
                return $tableDefinition->getTableName();
            })->toArray();

            $resolver = new DependencyResolver($tableDefinitions);
            [$nonCirculars, $circulars] = $resolver->getDependencyOrder();

            foreach ($nonCirculars as $nonCircular) {
                [$nonCircularTable, $nonCircularColumns] = explode('.', $nonCircular);
                $finalOrder[] = $keyedTableDefinitions[$nonCircularTable];
            }
            $processedCirculars = [];

            foreach ($circulars as $circular) {
                [$table, $dependency] = $circular;

                if (isset($processedCirculars[implode(',', $circular)]) || isset($processedCirculars[implode(',', array_reverse($circular))])) {
                    continue;
                }

                $processedCirculars[implode(',', $circular)] = true;

                [$tableName, $columns] = explode('.', $table);
                $columns = explode(DependencyResolver::SEPARATOR, $columns);

                [$dependencyTableName, $dependencyColumns] = explode('.', $dependency);
                $dependencyColumns = explode(DependencyResolver::SEPARATOR, $dependencyColumns);

                /** @var TableDefinition $tableInstance */
                $tableInstance = $keyedTableDefinitions[$tableName];

                /** @var TableDefinition $dependencyInstance */
                $dependencyInstance = $keyedTableDefinitions[$dependencyTableName];

                //grab indices from the table
                //and remove them from the instance
                $tableIndices = collect($tableInstance->getIndexDefinitions())
                    ->filter(function (IndexDefinition $definition) use ($dependencyTableName, $dependencyColumns) {
                        return $definition->getForeignReferencedTable() == $dependencyTableName && count(array_intersect($dependencyColumns, $definition->getForeignReferencedColumns())) > 0;
                    })->each(function ($indexDefinition) use ($tableInstance) {
                        $tableInstance->removeIndexDefinition($indexDefinition);
                    });
                $finalOrder[] = $tableInstance;

                $dependencyIndices = collect($dependencyInstance->getIndexDefinitions())
                    ->filter(function (IndexDefinition $definition) use ($tableName, $columns) {
                        return $definition->getForeignReferencedTable() == $tableName && count(array_intersect($columns, $definition->getForeignReferencedColumns())) > 0;
                    })->each(function ($indexDefinition) use ($dependencyInstance) {
                        $dependencyInstance->removeIndexDefinition($indexDefinition);
                    });
                $finalOrder[] = $dependencyInstance;

                $finalOrder[] = new TableDefinition([
                    'tableName'         => $tableInstance->getTableName(),
                    'driver'            => $tableInstance->getDriver(),
                    'columnDefinitions' => [],
                    'indexDefinitions'  => $tableIndices->toArray()
                ]);

                $finalOrder[] = new TableDefinition([
                    'tableName'         => $dependencyInstance->getTableName(),
                    'driver'            => $dependencyInstance->getDriver(),
                    'columnDefinitions' => [],
                    'indexDefinitions'  => $dependencyIndices->toArray()
                ]);
            }

            return $finalOrder;
        }

        return $tableDefinitions;
    }

    /**
     * @param array<TableDefinition> $tableDefinitions
     * @param $basePath
     */
    public function writeTableMigrations(array $tableDefinitions, $basePath)
    {
        foreach ($tableDefinitions as $tableDefinition) {
            $tableDefinition->write($basePath);
        }
    }

    /**
     * @param array<BaseViewGenerator> $viewDefinitions
     * @param $basePath
     */
    public function writeViewMigrations(array $viewDefinitions, $basePath)
    {
        foreach ($viewDefinitions as $view) {
            $view->write($basePath);
        }
    }

    /**
     * @return array<string>
     */
    public function skippableTables(): array
    {
        return ConfigResolver::skippableTables(static::driver());
    }

    public function skipTable($table): bool
    {
        return in_array($table, $this->skippableTables());
    }

    /**
     * @return array<string>
     */
    public function skippableViews(): array
    {
        return ConfigResolver::skippableViews(static::driver());
    }

    public function skipView($view): bool
    {
        $skipViews = config('laravel-migration-generator.skip_views');
        if ($skipViews) {
            return true;
        }

        return ! in_array($view, $this->skippableViews());
    }
}
