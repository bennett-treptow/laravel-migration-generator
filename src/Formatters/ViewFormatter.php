<?php

namespace LaravelMigrationGenerator\Formatters;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Helpers\Formatter;
use LaravelMigrationGenerator\Helpers\ConfigResolver;
use LaravelMigrationGenerator\Definitions\ViewDefinition;

class ViewFormatter
{
    private ViewDefinition $definition;

    public function __construct(ViewDefinition $definition)
    {
        $this->definition = $definition;
    }

    public function stubNameVariables($index = 0)
    {
        return [
            'ViewName:Studly'       => Str::studly($viewName = $this->definition->getViewName()),
            'ViewName:Lowercase'    => strtolower($viewName),
            'ViewName'              => $viewName,
            'Timestamp'             => app('laravel-migration-generator:time')->format('Y_m_d_His'),
            'Index'                 => '0000_00_00_' . str_pad((string) $index, 6, '0', STR_PAD_LEFT),
            'IndexedEmptyTimestamp' => '0000_00_00_' . str_pad((string) $index, 6, '0', STR_PAD_LEFT),
            'IndexedTimestamp'      => app('laravel-migration-generator:time')->clone()->addSeconds($index)->format('Y_m_d_His')
        ];
    }

    protected function getStubFileName($index = 0)
    {
        $driver = $this->definition->getDriver();

        $baseStubFileName = ConfigResolver::viewNamingScheme($driver);
        foreach ($this->stubNameVariables($index) as $variable => $replacement) {
            if (preg_match("/\[" . $variable . "\]/i", $baseStubFileName) === 1) {
                $baseStubFileName = preg_replace("/\[" . $variable . "\]/i", $replacement, $baseStubFileName);
            }
        }

        return $baseStubFileName;
    }

    protected function getStubPath()
    {
        $driver = $this->definition->getDriver();

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/' . $driver . '-view.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('stubs/vendor/laravel-migration-generator/view.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../stubs/view.stub';
    }

    public function render($tabCharacter = '    ')
    {
        $schema = $this->definition->getSchema();
        $stub = file_get_contents($this->getStubPath());
        $variables = [
            '[ViewName:Studly]' => Str::studly($viewName = $this->definition->getViewName()),
            '[ViewName]'        => $viewName,
            '[Schema]'          => $schema
        ];
        foreach ($variables as $key => $value) {
            $stub = Formatter::replace($tabCharacter, $key, $value, $stub);
        }

        return $stub;
    }

    public function write(string $basePath, $index = 0, string $tabCharacter = '    '): string
    {
        $stub = $this->render($tabCharacter);

        $fileName = $this->getStubFileName($index);
        file_put_contents($final = $basePath . '/' . $fileName, $stub);

        return $final;
    }
}
