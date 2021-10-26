<?php

namespace LaravelMigrationGenerator\Definitions;

use LaravelMigrationGenerator\Formatters\TableFormatter;

class TableDefinition
{
    protected string $tableName;

    protected string $driver;

    /** @var array<ColumnDefinition> */
    protected array $columnDefinitions = [];

    protected array $indexDefinitions = [];

    public function __construct($attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function getPresentableTableName(): string
    {
        if (count($this->getColumnDefinitions()) === 0) {
            if (count($definitions = $this->getIndexDefinitions()) > 0) {
                $first = collect($definitions)->first();
                //a fk only table from dependency resolution
                return $this->getTableName() . '_' . $first->getIndexName();
            }
        }

        return $this->getTableName();
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function getColumnDefinitions(): array
    {
        return $this->columnDefinitions;
    }

    public function setColumnDefinitions(array $columnDefinitions)
    {
        $this->columnDefinitions = $columnDefinitions;

        return $this;
    }

    public function addColumnDefinition(ColumnDefinition $definition)
    {
        $this->columnDefinitions[] = $definition;

        return $this;
    }

    /**
     * @return array<IndexDefinition>
     */
    public function getIndexDefinitions(): array
    {
        return $this->indexDefinitions;
    }

    /** @return array<IndexDefinition> */
    public function getForeignKeyDefinitions(): array
    {
        return collect($this->getIndexDefinitions())->filter(function ($indexDefinition) {
            return $indexDefinition->getIndexType() == IndexDefinition::TYPE_FOREIGN;
        })->toArray();
    }

    public function setIndexDefinitions(array $indexDefinitions)
    {
        $this->indexDefinitions = $indexDefinitions;

        return $this;
    }

    public function addIndexDefinition(IndexDefinition $definition)
    {
        $this->indexDefinitions[] = $definition;

        return $this;
    }

    public function removeIndexDefinition(IndexDefinition $definition)
    {
        foreach ($this->indexDefinitions as $key => $indexDefinition) {
            if ($definition->getIndexName() == $indexDefinition->getIndexName()) {
                unset($this->indexDefinitions[$key]);

                break;
            }
        }

        return $this;
    }

    public function getPrimaryKey(): array
    {
        return collect($this->getColumnDefinitions())
            ->filter(function (ColumnDefinition $columnDefinition) {
                return $columnDefinition->isPrimary();
            })->toArray();
    }

    public function formatter(): TableFormatter
    {
        return new TableFormatter($this);
    }
}
