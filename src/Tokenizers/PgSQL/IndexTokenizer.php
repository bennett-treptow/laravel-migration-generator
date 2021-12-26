<?php

namespace BennettTreptow\LaravelMigrationGenerator\Tokenizers\PgSQL;

use BennettTreptow\LaravelMigrationGenerator\Tokenizers\BaseIndexTokenizer;

class IndexTokenizer extends BaseIndexTokenizer
{
    public function tokenize(): self
    {
        $this->consume(); // Just CONSTRAINT
        $this->consumeIndexName();
        $this->consumeIndexType();

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
        } elseif ($upper === 'FOREIGN') {
            $this->definition->setIndexType('foreign');
        } else {
            dd('null', $this, $piece);
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
        $this->consume(); // KEY

        $columns = $this->columnsToArray($this->consume());

        $this->definition->setIndexColumns($columns);

        $this->consume(); //REFERENCES

        $referenced = explode('(', $this->parseColumn($this->consume()));
        $this->definition->setForeignReferencedTable($referenced[0]);

        $referencedColumns = $this->columnsToArray($referenced[1]);
        $this->definition->setForeignReferencedColumns($referencedColumns);

        $this->consumeConstraintActions();
    }

    private function consumeConstraintActions()
    {
        while ($token = $this->consume()) {
            if (strtoupper($token) === 'ON') {
                $actionType = strtolower($this->consume()); //UPDATE
                $actionMethod = strtolower($this->consume()); //CASCADE | NO ACTION | SET NULL | SET DEFAULT
                if ($actionMethod === 'no') {
                    $this->consume(); //consume ACTION
                    $actionMethod = 'restrict';
                } elseif ($actionMethod === 'set') {
                    $actionMethod = 'set ' . $this->consume(); //consume NULL or DEFAULT
                }
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
