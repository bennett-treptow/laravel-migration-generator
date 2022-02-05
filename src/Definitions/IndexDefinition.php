<?php

namespace LaravelMigrationGenerator\Definitions;

use LaravelMigrationGenerator\Helpers\ValueToString;
use LaravelMigrationGenerator\Helpers\WritableTrait;

class IndexDefinition
{
    const TYPE_FOREIGN = 'foreign';

    use WritableTrait;

    protected string $indexType;

    protected ?string $indexName = null; //primary keys usually don't have a name

    protected array $indexColumns = [];

    protected array $foreignReferencedColumns = [];

    protected string $foreignReferencedTable;

    protected array $constraintActions = [];

    public function __construct($attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    //region Getters

    /**
     * @return string
     */
    public function getIndexType(): string
    {
        return $this->indexType;
    }

    /**
     * @return string|null
     */
    public function getIndexName(): ?string
    {
        return $this->indexName;
    }

    /**
     * @return array
     */
    public function getIndexColumns(): array
    {
        return $this->indexColumns;
    }

    /**
     * @return array
     */
    public function getForeignReferencedColumns(): array
    {
        return $this->foreignReferencedColumns;
    }

    /**
     * @return string
     */
    public function getForeignReferencedTable(): string
    {
        return $this->foreignReferencedTable;
    }

    /**
     * @return array
     */
    public function getConstraintActions(): array
    {
        return $this->constraintActions;
    }

    //endregion
    //region Setters

    /**
     * @param string $indexType
     * @return IndexDefinition
     */
    public function setIndexType(string $indexType): IndexDefinition
    {
        $this->indexType = $indexType;

        return $this;
    }

    /**
     * @param string $indexName
     * @return IndexDefinition
     */
    public function setIndexName(string $indexName): IndexDefinition
    {
        $this->indexName = $indexName;

        return $this;
    }

    /**
     * @param array $indexColumns
     * @return IndexDefinition
     */
    public function setIndexColumns(array $indexColumns): IndexDefinition
    {
        $this->indexColumns = $indexColumns;

        return $this;
    }

    /**
     * @param array $foreignReferencedColumns
     * @return IndexDefinition
     */
    public function setForeignReferencedColumns(array $foreignReferencedColumns): IndexDefinition
    {
        $this->foreignReferencedColumns = $foreignReferencedColumns;

        return $this;
    }

    /**
     * @param string $foreignReferencedTable
     * @return IndexDefinition
     */
    public function setForeignReferencedTable(string $foreignReferencedTable): IndexDefinition
    {
        $this->foreignReferencedTable = $foreignReferencedTable;

        return $this;
    }

    /**
     * @param array $constraintActions
     * @return IndexDefinition
     */
    public function setConstraintActions(array $constraintActions): IndexDefinition
    {
        $this->constraintActions = $constraintActions;

        return $this;
    }

    //endregion

    public function isMultiColumnIndex()
    {
        return count($this->indexColumns) > 1;
    }

    public function render(): string
    {
        if ($this->indexType === 'foreign') {
            $indexName = '';
            if (config('laravel-migration-generator.definitions.use_defined_foreign_key_index_names')) {
                $indexName = ', \'' . $this->getIndexName() . '\'';
            }

            $base = '$table->foreign(' . ValueToString::make($this->indexColumns, true) . $indexName . ')->references(' . ValueToString::make($this->foreignReferencedColumns, true) . ')->on(' . ValueToString::make($this->foreignReferencedTable) . ')';
            foreach ($this->constraintActions as $type => $action) {
                $base .= '->on' . ucfirst($type) . '(' . ValueToString::make($action) . ')';
            }

            return $base;
        } elseif ($this->indexType === 'primary') {
            $indexName = '';
            if (config('laravel-migration-generator.definitions.use_defined_primary_key_index_names') && $this->getIndexName() !== null) {
                $indexName = ', \'' . $this->getIndexName() . '\'';
            }

            return '$table->primary(' . ValueToString::make($this->indexColumns) . $indexName . ')';
        } elseif ($this->indexType === 'unique') {
            $indexName = '';
            if (config('laravel-migration-generator.definitions.use_defined_unique_key_index_names')) {
                $indexName = ', \'' . $this->getIndexName() . '\'';
            }

            return '$table->unique(' . ValueToString::make($this->indexColumns) . $indexName . ')';
        } elseif ($this->indexType === 'index') {
            $indexName = '';
            if (config('laravel-migration-generator.definitions.use_defined_index_names')) {
                $indexName = ', \'' . $this->getIndexName() . '\'';
            }

            return '$table->index(' . ValueToString::make($this->indexColumns) . $indexName . ')';
        }

        return '';
    }
}
