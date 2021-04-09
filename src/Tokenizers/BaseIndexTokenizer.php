<?php
namespace LaravelMigrationGenerator\Tokenizers;

abstract class BaseIndexTokenizer extends BaseTokenizer implements IndexTokenizerInterface {
    protected string $indexType;

    protected string $indexName;

    protected $indexColumns = [];

    protected $foreignReferencedColumns;

    protected string $foreignReferencedTable;

    protected $constraintActions = [];

    protected $relatedColumns = [];

    public function getIndexType(): string{
        return $this->indexType;
    }

    public function getIndexName(): string{
        return $this->indexName;
    }

    public function getIndexColumns(){
        return $this->indexColumns;
    }

    public function getForeignReferencedColumns()
    {
        return $this->foreignReferencedColumns;
    }

    public function getForeignReferencedTable(): string
    {
        return $this->foreignReferencedTable;
    }

    public function getConstraintActions(): array
    {
        return $this->constraintActions;
    }

    public function isMultiColumnIndex()
    {
        return count($this->indexColumns) > 1;
    }

    public function column(ColumnTokenizerInterface $column)
    {
        $this->relatedColumns[] = $column;

        return $this;
    }

    public function toMethod(): string
    {
        if ($this->indexType === 'foreign') {
            $base = '$table->foreign(' . $this->valueToString($this->indexColumns, true) . ')->references(' . $this->valueToString($this->foreignReferencedColumns, true) . ')->on(' . $this->valueToString($this->foreignReferencedTable) . ')';
            foreach ($this->constraintActions as $type => $action) {
                $base .= '->on' . ucfirst($type) . '(' . $this->valueToString($action) . ')';
            }

            return $base;
        } elseif ($this->indexType === 'primary') {
            return '$table->primary(' . $this->valueToString($this->indexColumns) . ')';
        } elseif ($this->indexType === 'unique') {
            return '$table->unique(' . $this->valueToString($this->indexColumns) . ', ' . $this->valueToString($this->indexName) . ')';
        } elseif ($this->indexType === 'index') {
            return '$table->index(' . $this->valueToString($this->indexColumns) . ', ' . $this->valueToString($this->indexName) . ')';
        }

        return '';
    }
}