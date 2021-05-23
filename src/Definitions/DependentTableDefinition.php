<?php

namespace LaravelMigrationGenerator\Definitions;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Helpers\ConfigResolver;
use LaravelMigrationGenerator\Generators\Concerns\WritesToFile;

class DependentTableDefinition
{
    use WritesToFile;

    private array $tableDefinitions;

    public function __construct(array $tableDefinitions)
    {
        $this->tableDefinitions = $tableDefinitions;
    }

    protected function generateStub($tabCharacter = '    ')
    {
        $tab = str_repeat($tabCharacter, 4);
        $tablesUp = [];
        $tablesDown = [];
        foreach ($this->tableDefinitions as $tableDefinition) {
            /** @var TableDefinition $tableDefinition */
            $tablesUp[] = $tab . $tableDefinition->getFilledStubUp($tab . $tabCharacter);
            $tablesDown[] = $tab . $tableDefinition->getFilledStubDown($tab . $tabCharacter);
        }
        $stub = file_get_contents($this->getStubPath());
        $stub = str_replace('[TablesUp]', implode("\n", $tablesUp), $stub);
        $stub = str_replace('[TablesDown]', implode("\n", $tablesDown), $stub);
        foreach ($this->stubNameVariables() as $var => $replacement) {
            $stub = str_replace('[' . $var . ']', $replacement, $stub);
        }

        return $stub;
    }

    public function getDriver(): string
    {
        return $this->tableDefinitions[0]->getDriver();
    }

    protected function getStubFileName(): string
    {
        $driver = $this->getDriver();
        $baseStubFileName = ConfigResolver::dependentTableNamingScheme($driver);
        foreach ($this->stubNameVariables() as $variable => $replacement) {
            if (preg_match("/\[" . $variable . "\]/i", $baseStubFileName) === 1) {
                $baseStubFileName = preg_replace("/\[" . $variable . "\]/i", $replacement, $baseStubFileName);
            }
        }

        return $baseStubFileName;
    }

    protected function getStubPath(): string
    {
        $driver = $this->getDriver();

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/' . $driver . '-dependent-table.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/dependent-table.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../stubs/dependent-table.stub';
    }

    protected function stubNameVariables()
    {
        $tableNames = collect($this->tableDefinitions)->map(function ($tableDefinition) {
            return $tableDefinition->getTableName();
        });

        return  [
            'TableNames:Studly' => $tableNames->map(function ($name) {
                return Str::studly($name);
            })->join(''),
            'TableNames:Lowercase' => $tableNames->map(function ($name) {
                return Str::studly($name);
            })->join(''),
            'TableNames' => $tableNames->join(''),
            'Timestamp'  => app('laravel-migration-generator:time')->format('Y_m_d_His')
        ];
    }
}
