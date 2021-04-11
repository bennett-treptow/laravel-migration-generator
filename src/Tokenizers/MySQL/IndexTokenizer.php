<?php

namespace LaravelMigrationGenerator\Tokenizers\MySQL;

use LaravelMigrationGenerator\Tokenizers\BaseIndexTokenizer;

class IndexTokenizer extends BaseIndexTokenizer
{
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
}
