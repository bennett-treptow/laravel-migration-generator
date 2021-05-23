<?php

namespace LaravelMigrationGenerator\Generators\Concerns;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Helpers\ConfigResolver;

/**
 * Trait WritesTablesToFile
 * @package LaravelMigrationGenerator\Generators\Concerns
 */
trait WritesTablesToFile
{
    use WritesToFile;

    protected function generateStub($tabCharacter = '    ')
    {
        $tab = str_repeat($tabCharacter, 4);

        $tableName = $this->getTableName();

        $schema = $this->getSchema($tab);
        $stubPath = $this->getStubPath();
        $stub = file_get_contents($stubPath);
        if (strpos($stub, '[TableUp]') !== false) {
            //uses new syntax
            $stub = str_replace('[TableUp]', $tabCharacter . $this->getFilledStubUp($tab), $stub);
            $stub = str_replace('[TableDown]', $tabCharacter . $this->getFilledStubDown($tab), $stub);
        }

        $stub = str_replace('[TableName:Studly]', Str::studly($tableName), $stub);
        $stub = str_replace('[TableName]', $tableName, $stub);
        $stub = str_replace('[Schema]', $schema, $stub);

        return $stub;
    }

    protected function getStubFileName(): string
    {
        $driver = $this->getDriver();
        $baseStubFileName = ConfigResolver::tableNamingScheme($driver);
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

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/' . $driver . '-table.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/table.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../../stubs/table.stub';
    }

    public function getStubUpPath(): string
    {
        $driver = $this->getDriver();

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/' . $driver . '-table-up.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/table-up.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../../stubs/table-up.stub';
    }

    public function getStubDownPath(): string
    {
        $driver = $this->getDriver();

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/' . $driver . '-table-down.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/table-down.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../../stubs/table-down.stub';
    }

    protected function stubNameVariables(): array
    {
        $tableName = $this->getTableName();

        return [
            'TableName:Studly'    => Str::studly($tableName),
            'TableName:Lowercase' => strtolower($tableName),
            'TableName'           => $tableName,
            'Timestamp'           => app('laravel-migration-generator:time')->format('Y_m_d_His')
        ];
    }
}
