<?php

namespace LaravelMigrationGenerator\Formatters;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Helpers\Formatter;
use LaravelMigrationGenerator\Helpers\ConfigResolver;
use LaravelMigrationGenerator\Definitions\TableDefinition;

class TableFormatter
{
    private TableDefinition $tableDefinition;

    public function __construct(TableDefinition $tableDefinition)
    {
        $this->tableDefinition = $tableDefinition;
    }

    public function render($tabCharacter = '    ')
    {
        $tableName = $this->tableDefinition->getPresentableTableName();

        $schema = $this->getSchema($tabCharacter);
        $stub = file_get_contents($this->getStubPath());
        if (strpos($stub, '[TableUp]') !== false) {
            //uses new syntax
            $stub = Formatter::replace($tabCharacter, '[TableUp]', $this->stubTableUp($tabCharacter), $stub);
            $stub = Formatter::replace($tabCharacter, '[TableDown]', $this->stubTableDown($tabCharacter), $stub);
        }

        $stub = str_replace('[TableName:Studly]', Str::studly($tableName), $stub);
        $stub = str_replace('[TableName]', $tableName, $stub);
        $stub = Formatter::replace($tabCharacter, '[Schema]', $schema, $stub);

        return $stub;
    }

    public function getStubFileName($index = 0): string
    {
        $driver = $this->tableDefinition->getDriver();
        $baseStubFileName = ConfigResolver::tableNamingScheme($driver);
        foreach ($this->stubNameVariables($index) as $variable => $replacement) {
            if (preg_match("/\[" . $variable . "\]/i", $baseStubFileName) === 1) {
                $baseStubFileName = preg_replace("/\[" . $variable . "\]/i", $replacement, $baseStubFileName);
            }
        }

        return $baseStubFileName;
    }

    public function getStubPath(): string
    {
        $driver = $this->tableDefinition->getDriver();

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/' . $driver . '-table.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/table.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../stubs/table.stub';
    }

    public function getStubCreatePath(): string
    {
        $driver = $this->tableDefinition->getDriver();

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/' . $driver . '-table-create.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/table-create.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../stubs/table-create.stub';
    }

    public function getStubModifyPath(): string
    {
        $driver = $this->tableDefinition->getDriver();

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/' . $driver . '-table-modify.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/table-modify.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../stubs/table-modify.stub';
    }

    public function stubNameVariables($index): array
    {
        $tableName = $this->tableDefinition->getPresentableTableName();

        return [
            'TableName:Studly'      => Str::studly($tableName),
            'TableName:Lowercase'   => strtolower($tableName),
            'TableName'             => $tableName,
            'Timestamp'             => app('laravel-migration-generator:time')->format('Y_m_d_His'),
            'Index'                 => (string) $index,
            'IndexedEmptyTimestamp' => '0000_00_00_' . str_pad((string) $index, 6, '0', STR_PAD_LEFT),
            'IndexedTimestamp'      => app('laravel-migration-generator:time')->clone()->addSeconds($index)->format('Y_m_d_His')
        ];
    }

    public function getSchema($tab = ''): string
    {
        $formatter = new Formatter($tab);
        collect($this->tableDefinition->getColumnDefinitions())
            ->filter(fn ($col) => $col->isWritable())
            ->each(function ($column) use ($formatter) {
                $formatter->line($column->render() . ';');
            });

        $indices = collect($this->tableDefinition->getIndexDefinitions())
            ->filter(fn ($index) => $index->isWritable());

        if ($indices->count() > 0) {
            if (count($this->tableDefinition->getColumnDefinitions()) > 0) {
                $formatter->line('');
            }
            $indices->each(function ($index) use ($formatter) {
                $formatter->line($index->render() . ';');
            });
        }

        return $formatter->render();
    }

    public function stubTableUp($tab = '', $variables = null): string
    {
        if ($variables === null) {
            $variables = $this->getStubVariables($tab);
        }
        if (count($this->tableDefinition->getColumnDefinitions()) === 0) {
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

    public function stubTableDown($tab = ''): string
    {
        if (count($this->tableDefinition->getColumnDefinitions()) === 0) {
            $schema = 'Schema::table(\'' . $this->tableDefinition->getTableName() . '\', function(Blueprint $table){' . "\n";
            foreach ($this->tableDefinition->getForeignKeyDefinitions() as $indexDefinition) {
                $schema .= $tab . '$table->dropForeign(\'' . $indexDefinition->getIndexName() . '\');' . "\n";
            }

            return $schema . '});';
        }

        return 'Schema::dropIfExists(\'' . $this->tableDefinition->getTableName() . '\');';
    }

    protected function getStubVariables($tab = '')
    {
        $tableName = $this->tableDefinition->getTableName();

        return  [
            'TableName:Studly'    => Str::studly($tableName),
            'TableName:Lowercase' => strtolower($tableName),
            'TableName'           => $tableName,
            'Schema'              => $this->getSchema($tab)
        ];
    }

    public function write(string $basePath, $index = 0, string $tabCharacter = '    '): string
    {
        $stub = $this->render($tabCharacter);

        $fileName = $this->getStubFileName($index);
        file_put_contents($final = $basePath . '/' . $fileName, $stub);

        return $final;
    }
}
