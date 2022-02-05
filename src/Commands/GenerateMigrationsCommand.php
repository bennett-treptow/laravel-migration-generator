<?php

namespace LaravelMigrationGenerator\Commands;

use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use LaravelMigrationGenerator\Helpers\ConfigResolver;
use LaravelMigrationGenerator\GeneratorManagers\MySQLGeneratorManager;
use LaravelMigrationGenerator\GeneratorManagers\Interfaces\GeneratorManagerInterface;

class GenerateMigrationsCommand extends Command
{
    protected $signature = 'generate:migrations {--path=default : The path where migrations will be output to} {--table=* : Only generate output for specified tables} {--view=* : Only generate output for specified views} {--connection=default : Use a different database connection specified in database config} {--empty-path : Clear other files in path, eg if wanting to replace all migrations}';

    protected $description = 'Generate migrations from an existing database';

    public function getConnection()
    {
        $connection = $this->option('connection');

        if ($connection === 'default') {
            $connection = Config::get('database.default');
        }

        if (! Config::has('database.connections.' . $connection)) {
            throw new \Exception('Could not find connection `' . $connection . '` in your config.');
        }

        return $connection;
    }

    public function getPath($driver)
    {
        $basePath = $this->option('path');
        if ($basePath === 'default') {
            $basePath = ConfigResolver::path($driver);
        }

        return $basePath;
    }

    public function handle()
    {
        try {
            $connection = $this->getConnection();
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $this->info('Using connection ' . $connection);
        DB::setDefaultConnection($connection);

        $driver = Config::get('database.connections.' . $connection)['driver'];

        $manager = $this->resolveGeneratorManager($driver);
        if ($manager === false) {
            $this->error('The `' . $driver . '` driver is not supported at this time.');

            return 1;
        }

        $basePath = base_path($this->getPath($driver));

        if ($this->option('empty-path') || config('laravel-migration-generator.clear_output_path')) {
            foreach (glob($basePath . '/*.php') as $file) {
                unlink($file);
            }
        }

        $this->info('Using ' . $basePath . ' as the output path..');

        $tableNames = Arr::wrap($this->option('table'));

        $viewNames = Arr::wrap($this->option('view'));

        $manager->handle($basePath, $tableNames, $viewNames);
    }

    /**
     * @param string $driver
     * @return false|GeneratorManagerInterface
     */
    protected function resolveGeneratorManager(string $driver)
    {
        $supported = [
            'mysql' => MySQLGeneratorManager::class
        ];

        if (! isset($supported[$driver])) {
            return false;
        }

        return new $supported[$driver]();
    }
}
