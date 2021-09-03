<?php

namespace LaravelMigrationGenerator\Definitions;

use Illuminate\Support\Str;
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
            //a fk only table from dependency resolution
            return $this->getTableName() . '_' . $this->getIndexDefinitions()[0]->getIndexName();
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
        $schema = collect($this->getColumnDefinitions())
            ->filter(fn ($col) => $col->isWritable())
            ->map(function ($column) use ($tab) {
                return $tab . $column->render() . ';';
            })
            ->implode("\n");

        $indices = collect($this->getIndexDefinitions())
            ->filter(fn ($index) => $index->isWritable());

        if ($indices->count() > 0) {
            $schema .= "\n";
            $schema .= $indices
                ->map(function ($index) use ($tab) {
                    return $tab . $index->render() . ';';
                })
                ->implode("\n");
        }

        return $schema;
    }

    public function getFilledStubUp($tab = '', $variables = null)
    {
        if ($variables === null) {
            $variables = $this->getStubVariables($tab);
        }
        $tableUpStub = file_get_contents($this->getStubUpPath());
        foreach ($variables as $var => $replacement) {
            $tableUpStub = str_replace('[' . $var . ']', $replacement, $tableUpStub);
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

    public function getFilledStub($tab = '')
    {
        $variables = $this->getStubVariables($tab);

        $stub = file_get_contents($this->getStubPath());

        if (Str::contains($stub, '[TableUp]')) {
            $stub = str_replace('[TableUp]', $this->getFilledStubUp($tab, $variables), $stub);
        }

        if (Str::contains($stub, '[TableDown]')) {
            $stub = str_replace('[TableDown]', $this->getFilledStubDown($tab, $variables), $stub);
        }

        foreach ($variables as $var => $replacement) {
            $stub = str_replace('[' . $var . ']', $replacement, $stub);
        }

        return $stub;
    }

    public function getPrimaryKey(): array
    {
        return collect($this->getColumnDefinitions())
            ->filter(function (ColumnDefinition $columnDefinition) {
                return $columnDefinition->isPrimary();
            })->toArray();
    }
}
