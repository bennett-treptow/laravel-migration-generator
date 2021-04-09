<?php

namespace LaravelMigrationGenerator\Tokenizers\MySQL;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Generators\TableGeneratorInterface;
use LaravelMigrationGenerator\Tokenizers\BaseIndexTokenizer;
use LaravelMigrationGenerator\Tokenizers\WritableTokenizer;

class IndexTokenizer extends BaseIndexTokenizer
{
    use WritableTokenizer;

    public function tokenize(): self
    {
        $this->consumeIndexType();
        if ($this->indexType !== 'primary') {
            $this->consumeIndexName();
        }

        if ($this->indexType === 'foreign') {
            $this->consumeForeignKey();
        } else {
            $this->consumeIndexColumns();
        }

        return $this;
    }

    private function consumeIndexType()
    {
        $piece = $this->consume();
        $upper = strtoupper($piece);
        if (in_array($upper, ['PRIMARY', 'UNIQUE', 'FULLTEXT'])) {
            $this->indexType = strtolower($piece);
            $this->consume(); //just the word KEY
        } elseif ($upper === 'KEY') {
            $this->indexType = 'index';
        } elseif ($upper === 'CONSTRAINT') {
            $this->indexType = 'foreign';
        }
    }

    private function consumeIndexName()
    {
        $piece = $this->consume();
        $this->indexName = $this->parseColumn($piece);
    }

    private function consumeIndexColumns()
    {
        $piece = $this->consume();
        $columns = $this->columnsToArray($piece);

        $this->indexColumns = $columns;
    }

    private function consumeForeignKey()
    {
        $piece = $this->consume();
        if (strtoupper($piece) === 'FOREIGN') {
            $this->consume(); //KEY

            $columns = $this->columnsToArray($this->consume());
            $this->indexColumns = $columns;

            $this->consume(); //REFERENCES

            $referencedTable = $this->parseColumn($this->consume());
            $this->foreignReferencedTable = $referencedTable;

            $referencedColumns = $this->columnsToArray($this->consume());
            $this->foreignReferencedColumns = $referencedColumns;

            $this->consumeConstraintActions();
        } else {
            $this->putBack($piece);
        }
    }

    private function consumeConstraintActions()
    {
        while ($token = $this->consume()) {
            if (strtoupper($token) === 'ON') {
                $actionType = strtolower($this->consume());
                $actionMethod = strtolower($this->consume());
                $this->constraintActions[$actionType] = $actionMethod;
            } else {
                $this->putBack($token);

                break;
            }
        }
    }

    public function finalPass(TableGeneratorInterface $table)
    {
        if ($this->getIndexType() === 'index') {
            //look for corresponding foreign key for this index
            $columns = $this->indexColumns;
            $table->indexIterator(function ($index) use ($columns) {
                if ($index->getIndexType() === 'foreign') {
                    $cols = $index->getIndexColumns();
                    if (count(array_intersect($columns, $cols)) === count($columns)) {
                        //has same columns
                        $this->markAsWritable(false);

                        return false;
                    }
                }
            });
        }
    }
}
