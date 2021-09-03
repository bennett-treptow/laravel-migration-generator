<?php

namespace LaravelMigrationGenerator\Helpers;

use PHPUnit\Framework\Assert;

class Dependency
{
    private string $tableName;

    protected array $dependents = [];

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function addDependent($columnOnDependency, string $tableName, $columnOnTable)
    {
        if (is_array($columnOnDependency)) {
            foreach ($columnOnDependency as $col) {
                $this->dependents[$col][$tableName][] = $columnOnTable;
            }
        } else {
            $this->dependents[$columnOnDependency][$tableName][] = $columnOnTable;
        }

        return $this;
    }

    public function getDependents()
    {
        return $this->dependents;
    }

    public function assertHasDependentTable($tableName)
    {
        foreach ($this->getDependents() as $column => $tables) {
            if (isset($tables[$tableName])) {
                return true;
            }
        }
        Assert::fail('Dependency does not have table ' . $tableName);
    }

    public function assertHasDependencyRelation($column, $tableName, $tableColumn)
    {
        Assert::assertTrue(isset($this->dependents[$column][$tableName]) && in_array($tableColumn, $this->dependents[$column][$tableName]), 'Dependency does not have relationship ' . $tableName . '.' . $tableColumn . ' for ' . $column);
    }
}
