<?php
namespace LaravelMigrationGenerator;

use Illuminate\Support\ServiceProvider;
use LaravelMigrationGenerator\Commands\MigrationsGenerationCommand;

class LaravelMigrationGeneratorProvider extends ServiceProvider {
    public function boot(){

        if($this->app->runningInConsole()){
            $this->commands([
                MigrationsGenerationCommand::class
            ]);
        }
    }
}