<?php
namespace LaravelMigrationGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use LaravelMigrationGenerator\GeneratorManagers\GeneratorManagerInterface;
use LaravelMigrationGenerator\GeneratorManagers\MySQLGeneratorManager;

class MigrationsGenerationCommand extends Command {
    protected $signature = 'migrate:generate {path} {--table=} {--connection=default}';

    public function handle(){
        $basePath = $this->argument('path');
        $connection = $this->option('connection');

        if($connection === 'default'){
            $connection = Config::get('database.default');
        }

        if(!Config::has('database.connections.'.$connection)){
            $this->error('Could not find connection `'.$connection.'` in your config.');

            return 1;
        }

        $this->info('Using connection '.$connection);

        $driver = Config::get('database.connections.'.$connection)['driver'];

        $manager = $this->resolveGeneratorManager($driver);
        if($manager === false){
            $this->error('The `'.$driver.'` driver is not supported at this time.');

            return 1;
        }

        $singleTableName = $this->option('table');

        $manager->handle($basePath, $singleTableName);
    }

    /**
     * @param string $driver
     * @return false|GeneratorManagerInterface
     */
    protected function resolveGeneratorManager(string $driver){
        $supported = [
            'mysql' => MySQLGeneratorManager::class
        ];

        if(!isset($supported[$driver])){
            return false;
        }
        return new $supported[$driver]();
    }
}