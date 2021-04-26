<?php

namespace LaravelMigrationGenerator;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Events\MigrationsEnded;
use LaravelMigrationGenerator\Commands\GenerateMigrationsCommand;

class LaravelMigrationGeneratorProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-migration-generator.php',
            'laravel-migration-generator'
        );

        $this->publishes([
            __DIR__ . '/../stubs'                                  => resource_path('stubs/vendor/laravel-migration-generator'),
            __DIR__ . '/../config/laravel-migration-generator.php' => config_path('laravel-migration-generator.php')
        ]);

        if ($this->app->runningInConsole()) {
            $this->app->instance('laravel-migration-generator:time', now());
            $this->commands([
                GenerateMigrationsCommand::class
            ]);
        }
        if (config('laravel-migration-generator.run_after_migrations') && config('app.env') === 'local') {
            Event::listen(MigrationsEnded::class, function () {
                Artisan::call('generate:migrations');
            });
        }
    }
}
