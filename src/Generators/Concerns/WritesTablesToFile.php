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

    public function generateStub($tabCharacter = '    ')
    {
        $tab = str_repeat($tabCharacter, 4);

        $tableName = $this->getPresentableTableName();

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

    public function getStubFileName($index = 0): string
    {
        $driver = $this->getDriver();
        $baseStubFileName = ConfigResolver::tableNamingScheme($driver);
        foreach ($this->stubNameVariables($index) as $variable => $replacement) {
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

    public function getStubCreatePath(): string
    {
        $driver = $this->getDriver();

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/' . $driver . '-table-create.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/table-create.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../../stubs/table-create.stub';
    }

    public function getStubModifyPath(): string
    {
        $driver = $this->getDriver();

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/' . $driver . '-table-modify.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/table-modify.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../../stubs/table-modify.stub';
    }

    protected function stubNameVariables($index): array
    {
        $tableName = $this->getPresentableTableName();

        return [
            'TableName:Studly'    => Str::studly($tableName),
            'TableName:Lowercase' => strtolower($tableName),
            'TableName'           => $tableName,
            'Timestamp'           => app('laravel-migration-generator:time')->format('Y_m_d_His'),
            'Index'               => '0000_00_00_' . str_pad((string) $index, 6, '0', STR_PAD_LEFT),
            'IndexedTimestamp'    => app('laravel-migration-generator:time')->clone()->addSeconds($index)->format('Y_m_d_His')
        ];
    }
}
