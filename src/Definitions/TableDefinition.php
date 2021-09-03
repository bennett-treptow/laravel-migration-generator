<?php

namespace LaravelMigrationGenerator\Definitions;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Helpers\Formatter;
use LaravelMigrationGenerator\Generators\Concerns\WritesTablesToFile;

class TableDefinition
{
    use WritesTablesToFile;

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
            if ($definition->getIndexName() == $definition->getIndexName()) {
                unset($this->indexDefinitions[$key]);

                break;
            }
        }

        return $this;
    }

    public function getSchema($tab = ''): string
    {
        $formatter = new Formatter($tab);
        collect($this->getColumnDefinitions())
            ->filter(fn ($col) => $col->isWritable())
            ->each(function ($column) use ($formatter) {
                $formatter->line($column->render() . ';');
            });

        $indices = collect($this->getIndexDefinitions())
            ->filter(fn ($index) => $index->isWritable());

        if ($indices->count() > 0) {
            if (count($this->getColumnDefinitions()) > 0) {
                $formatter->line('');
            }
            $indices->each(function ($index) use ($formatter) {
                $formatter->line($index->render() . ';');
            });
        }

        return $formatter->render();
    }

    public function getStubUp($tab = '')
    {
        if (count($this->getColumnDefinitions()) === 0) {
            return file_get_contents($this->getStubModifyPath());
        }

        return file_get_contents($this->getStubCreatePath());
    }

    public function getFilledStubUp($tab = '', $variables = null)
    {
        if ($variables === null) {
            $variables = $this->getStubVariables($tab);
        }
        if (count($this->getColumnDefinitions()) === 0) {
            $tableModifyStub = file_get_contents($this->getStubModifyPath());
            foreach ($variables as $var => $replacement) {
                $tableModifyStub = Formatter::replace($tab, '[' . $var . ']', $replacement, $tableModifyStub);
            }

            return $tableModifyStub;
        }

        $tableUpStub = file_get_contents($this->getStubCreatePath());
        foreach ($variables as $var => $replacement) {
            $tableUpStub = Formatter::replace($tab, '[' . $var . ']', $replacement, $tableUpStub);
        }

        return $tableUpStub;
    }

    public function getFilledStubDown($tab = '', $variables = null)
    {
        if (count($this->getColumnDefinitions()) === 0) {
            $schema = 'Schema::table(\'' . $this->getTableName() . '\', function(Blueprint $table){' . "\n";
            foreach ($this->getIndexDefinitions() as $indexDefinition) {
                $schema .= $tab . '$table->dropForeign(\'' . $indexDefinition->getIndexName() . '\');' . "\n";
            }

            return $schema . '});';
        }

        return 'Schema::dropIfExists(\'' . $this->getTableName() . '\');';
    }

    protected function getStubVariables($tab = '')
    {
        $tableName = $this->getTableName();

        return  [
            'TableName:Studly'    => Str::studly($tableName),
            'TableName:Lowercase' => strtolower($tableName),
            'TableName'           => $tableName,
            'Schema'              => $this->getSchema($tab)
        ];
    }

    public function getPrimaryKey(): array
    {
        return collect($this->getColumnDefinitions())
            ->filter(function (ColumnDefinition $columnDefinition) {
                return $columnDefinition->isPrimary();
            })->toArray();
    }
}
