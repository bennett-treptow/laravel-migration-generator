<?php

namespace LaravelMigrationGenerator;

use Illuminate\Support\ServiceProvider;
use LaravelMigrationGenerator\Commands\MigrationsGenerationCommand;

class LaravelMigrationGeneratorProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-migration-generator.php',
            'laravel-migration-generator'
        );

        $this->publishes([
            __DIR__ . '/../stubs'                                  => resource_path('views/vendor/laravel-migration-generator'),
            __DIR__ . '/../config/laravel-migration-generator.php' => config_path('laravel-migration-generator.php')
        ]);

        if ($this->app->runningInConsole()) {
            $this->app->instance('laravel-migration-generator:time', now());
            $this->commands([
                MigrationsGenerationCommand::class
            ]);
        }
    }
}
