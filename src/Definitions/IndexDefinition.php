<?php

namespace LaravelMigrationGenerator\Definitions;

use LaravelMigrationGenerator\Helpers\ValueToString;

class IndexDefinition
{
    protected string $indexType;

    protected string $indexName;

    protected array $indexColumns = [];

    protected array $foreignReferencedColumns = [];

    protected string $foreignReferencedTable;

    protected array $constraintActions = [];

    //region Getters

    /**
     * @return string
     */
    public function getIndexType(): string
    {
        return $this->indexType;
    }

    /**
     * @return string
     */
    public function getIndexName(): string
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

    public function render(): string{
        if ($this->indexType === 'foreign') {
            $base = '$table->foreign(' . ValueToString::make($this->indexColumns, true) . ', ' . ValueToString::make($this->indexName) . ')->references(' . ValueToString::make($this->foreignReferencedColumns, true) . ')->on(' . ValueToString::make($this->foreignReferencedTable) . ')';
            foreach ($this->constraintActions as $type => $action) {
                $base .= '->on' . ucfirst($type) . '(' . ValueToString::make($action) . ')';
            }

            return $base;
        } elseif ($this->indexType === 'primary') {
            return '$table->primary(' . ValueToString::make($this->indexColumns) . ')';
        } elseif ($this->indexType === 'unique') {
            return '$table->unique(' . ValueToString::make($this->indexColumns) . ', ' . ValueToString::make($this->indexName) . ')';
        } elseif ($this->indexType === 'index') {
            return '$table->index(' . ValueToString::make($this->indexColumns) . ', ' . ValueToString::make($this->indexName) . ')';
        }

        return '';
    }
}
