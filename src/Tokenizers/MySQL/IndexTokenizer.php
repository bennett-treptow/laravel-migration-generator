<?php

namespace LaravelMigrationGenerator\Tokenizers\MySQL;

use LaravelMigrationGenerator\Tokenizers\BaseIndexTokenizer;
use LaravelMigrationGenerator\Tokenizers\Traits\WritableTokenizer;
use LaravelMigrationGenerator\Generators\Interfaces\TableGeneratorInterface;

class IndexTokenizer extends BaseIndexTokenizer
{
    use WritableTokenizer;

    public function tokenize(): self
    {
        $this->consumeIndexType();
        if ($this->definition->getIndexType() !== 'primary') {
            $this->consumeIndexName();
        }

        if ($this->definition->getIndexType() === 'foreign') {
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
            $this->definition->setIndexType(strtolower($piece));
            $this->consume(); //just the word KEY
        } elseif ($upper === 'KEY') {
            $this->definition->setIndexType('index');
        } elseif ($upper === 'CONSTRAINT') {
            $this->definition->setIndexType('foreign');
        }
    }

    private function consumeIndexName()
    {
        $piece = $this->consume();
        $this->definition->setIndexName($this->parseColumn($piece));
    }

    private function consumeIndexColumns()
    {
        $piece = $this->consume();
        $columns = $this->columnsToArray($piece);

        $this->definition->setIndexColumns($columns);
    }

    private function consumeForeignKey()
    {
        $piece = $this->consume();
        if (strtoupper($piece) === 'FOREIGN') {
            $this->consume(); //KEY

            $columns = $this->columnsToArray($this->consume());
            $this->definition->setIndexColumns($columns);

            $this->consume(); //REFERENCES

            $referencedTable = $this->parseColumn($this->consume());
            $this->definition->setForeignReferencedTable($referencedTable);

            $referencedColumns = $this->columnsToArray($this->consume());
            $this->definition->setForeignReferencedColumns($referencedColumns);

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
                $currentActions = $this->definition->getConstraintActions();
                $currentActions[$actionType] = $actionMethod;
                $this->definition->setConstraintActions($currentActions);
            } else {
                $this->putBack($token);

                break;
            }
        }
    }

    public function finalPass(TableGeneratorInterface $table): ?bool
    {
        if ($this->definition->getIndexType() === 'index') {
            //look for corresponding foreign key for this index
            $columns = $this->definition->getIndexColumns();
            $table->indexIterator(function ($index) use ($columns) {
                if ($index->definition()->getIndexType() === 'foreign') {
                    $cols = $index->definition()->getIndexColumns();
                    if (count(array_intersect($columns, $cols)) === count($columns)) {
                        //has same columns
                        $this->markAsWritable(false);

                        return false;
                    }
                }
            });
        }

        return null;
    }
}
