<?php

namespace LaravelMigrationGenerator\Helpers;

use MJS\TopSort\Implementations\FixedArraySort;
use LaravelMigrationGenerator\Definitions\IndexDefinition;
use LaravelMigrationGenerator\Definitions\TableDefinition;

class DependencyResolver
{
    /** @var array<TableDefinition> */
    protected array $tableDefinitions = [];

    /** @var array<TableDefinition> */
    protected array $sorted = [];

    public function __construct(array $tableDefinitions)
    {
        $this->tableDefinitions = $tableDefinitions;

        $this->build();
    }

    protected function build()
    {
        /** @var TableDefinition[] $keyedDefinitions */
        $keyedDefinitions = collect($this->tableDefinitions)
            ->keyBy(function (TableDefinition $tableDefinition) {
                return $tableDefinition->getTableName();
            });
        $dependencies = [];
        foreach ($this->tableDefinitions as $tableDefinition) {
            $dependencies[$tableDefinition->getTableName()] = [];
        }
        foreach ($this->tableDefinitions as $tableDefinition) {
            foreach ($tableDefinition->getForeignKeyDefinitions() as $indexDefinition) {
                if (! in_array($indexDefinition->getForeignReferencedTable(), $dependencies[$tableDefinition->getTableName()])) {
                    $dependencies[$tableDefinition->getTableName()][] = $indexDefinition->getForeignReferencedTable();
                }
            }
        }

        $sorter = new FixedArraySort();
        $circulars = [];
        $sorter->setCircularInterceptor(function ($nodes) use (&$circulars) {
            $circulars[] = [$nodes[count($nodes) - 2], $nodes[count($nodes) - 1]];
        });
        foreach ($dependencies as $table => $dependencyArray) {
            $sorter->add($table, $dependencyArray);
        }
        $sorted = $sorter->sort();
        $definitions = collect($sorted)->map(function ($item) use ($keyedDefinitions) {
            return $keyedDefinitions[$item];
        })->toArray();

        foreach ($circulars as $groups) {
            [$start, $end] = $groups;
            $startDefinition = $keyedDefinitions[$start];
            $indicesForStart = collect($startDefinition->getForeignKeyDefinitions())
                ->filter(function (IndexDefinition $index) use ($end) {
                    return $index->getForeignReferencedTable() == $end;
                });
            foreach ($indicesForStart as $index) {
                $startDefinition->removeIndexDefinition($index);
            }
            if (! in_array($start, $sorted)) {
                $definitions[] = $startDefinition;
            }

            $endDefinition = $keyedDefinitions[$end];

            $indicesForEnd = collect($endDefinition->getForeignKeyDefinitions())
                ->filter(function (IndexDefinition $index) use ($start) {
                    return $index->getForeignReferencedTable() == $start;
                });
            foreach ($indicesForEnd as $index) {
                $endDefinition->removeIndexDefinition($index);
            }
            if (! in_array($end, $sorted)) {
                $definitions[] = $endDefinition;
            }

            $definitions[] = new TableDefinition([
                'tableName'         => $startDefinition->getTableName(),
                'driver'            => $startDefinition->getDriver(),
                'columnDefinitions' => [],
                'indexDefinitions'  => $indicesForStart->toArray()
            ]);

            $definitions[] = new TableDefinition([
                'tableName'         => $endDefinition->getTableName(),
                'driver'            => $endDefinition->getDriver(),
                'columnDefinitions' => [],
                'indexDefinitions'  => $indicesForEnd->toArray()
            ]);
        }
        $this->sorted = $definitions;
    }

    /**
     * @return TableDefinition[]
     */
    public function getDependencyOrder(): array
    {
        return $this->sorted;
    }
}
