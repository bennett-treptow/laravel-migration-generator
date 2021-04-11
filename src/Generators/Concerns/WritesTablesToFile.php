<?php

namespace LaravelMigrationGenerator\Generators\Concerns;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Helpers\ConfigResolver;
use LaravelMigrationGenerator\Generators\Interfaces\TableGeneratorInterface;

/**
 * Trait WritesTablesToFile
 * @package LaravelMigrationGenerator\Generators\Concerns
 * @mixin TableGeneratorInterface
 */
trait WritesTablesToFile
{
    use WritesToFile;

    protected function generateStub(string $stubPath, $tabCharacter = '    ')
    {
        $tab = str_repeat($tabCharacter, 3);

        $schema = $this->getSchema($tab);
        $stub = file_get_contents($stubPath);
        $stub = str_replace('[TableName:Studly]', Str::studly($this->tableName), $stub);
        $stub = str_replace('[TableName]', $this->tableName, $stub);
        $stub = str_replace('[Schema]', $schema, $stub);

        return $stub;
    }

    protected function getStubFileName(): string
    {
        $driver = static::driver();
        $baseStubFileName = ConfigResolver::tableNamingScheme($driver);
        foreach ($this->stubNameVariables() as $variable => $replacement) {
            if (is_callable($replacement)) {
                //replacement is a closure
                [$variable, $replacement] = $replacement($baseStubFileName);
            }
            if ($variable === null) {
                continue;
            }
            $baseStubFileName = preg_replace("/\[" . $variable . "\]/i", $replacement, $baseStubFileName);
        }

        return $baseStubFileName;
    }

    protected function getStubPath(): string
    {
        $driver = static::driver();

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/' . $driver . '-table.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/table.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../../stubs/table.stub';
    }

    protected function stubNameVariables(): array
    {
        return [
            'TableName:Studly'    => Str::studly($this->tableName),
            'TableName:Lowercase' => strtolower($this->tableName),
            'TableName'           => $this->tableName,
            'Timestamp'           => app('laravel-migration-generator:time')->format('Y_m_d_His'),
            'Timestamp:(.+?)'     => function ($parameter) {
                if (preg_match('/\[Timestamp:(.+?)\]/i', $parameter, $matches) !== 0) {
                    $format = $matches[1];

                    return ['Timestamp:' . $format, app('laravel-migration-generator:time')->format($format)];
                } else {
                    return [null, null];
                }
            }
        ];
    }
}
