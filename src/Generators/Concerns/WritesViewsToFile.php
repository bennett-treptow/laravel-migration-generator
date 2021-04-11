<?php

namespace LaravelMigrationGenerator\Generators\Concerns;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Helpers\ConfigResolver;

trait WritesViewsToFile
{
    use WritesToFile;

    public function stubNameVariables()
    {
        return [
            'ViewName:Studly'    => Str::studly($this->viewName),
            'ViewName:Lowercase' => strtolower($this->viewName),
            'ViewName'           => $this->viewName,
            'Timestamp'          => app('laravel-migration-generator:time')->format('Y_m_d_His'),
            'Timestamp:(.+?)'    => function ($parameter) {
                if (preg_match('/\[Timestamp:(.+?)\]/i', $parameter, $matches) !== false) {
                    $format = $matches[1];

                    return ['Timestamp:' . $format, app('laravel-migration-generator:time')->format($format)];
                } else {
                    return [null, null];
                }
            }
        ];
    }

    protected function getStubFileName()
    {
        $driver = static::driver();

        $baseStubFileName = ConfigResolver::viewNamingScheme($driver);
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

    protected function getStubPath()
    {
        $driver = static::driver();

        if (file_exists($overridden = resource_path('views/vendor/laravel-migration-generator/' . $driver . '-view.stub'))) {
            return $overridden;
        }

        if (file_exists($overridden = resource_path('views/vendor/laravel-migration-generator/view.stub'))) {
            return $overridden;
        }

        return __DIR__ . '/../../../stubs/view.stub';
    }

    protected function generateStub(string $stubPath, $tabCharacter = '    ')
    {
        $tab = str_repeat($tabCharacter, 3);

        $schema = $this->getSchema();
        $stub = file_get_contents($stubPath);
        $stub = str_replace('[ViewName]', Str::studly($this->viewName), $stub);
        $stub = str_replace('[View]', $this->viewName, $stub);
        $stub = str_replace('[Schema]', $tab . $schema, $stub);

        return $stub;
    }
}
