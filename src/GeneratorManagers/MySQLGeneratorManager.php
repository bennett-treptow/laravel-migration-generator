<?php

namespace LaravelMigrationGenerator\GeneratorManagers;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\OutputStyle;
use LaravelMigrationGenerator\Helpers\ConfigResolver;
use LaravelMigrationGenerator\Generators\MySQL\ViewGenerator;
use LaravelMigrationGenerator\Generators\MySQL\TableGenerator;
use LaravelMigrationGenerator\GeneratorManagers\Interfaces\GeneratorManagerInterface;

class MySQLGeneratorManager extends BaseGeneratorManager implements GeneratorManagerInterface
{
    public static function driver(): string
    {
        return 'mysql';
    }

    public function handle(string $basePath, OutputStyle $output, array $tableNames = [])
    {
        $this->createMissingDirectory($basePath);

        $skippableTables = ConfigResolver::skippableTables('mysql');
        $skipViews = config('laravel-migration-generator.skip_views');
        $skippableViews = ! $skipViews ? ConfigResolver::skippableViews('mysql') : [];
        $outputQueue = [];

        $tableDefinitions = [];
        $views = [];

        if (count($tableNames) > 0) {
            $progressBar = $output->createProgressBar(count($tableNames));
            foreach ($tableNames as $tableName) {
                if (in_array($tableName, $skippableTables)) {
                    $progressBar->advance();
                    $outputQueue[] = 'Skipped `' . $tableName . '` table';

                    continue;
                }

                $tableDefinitions[] = TableGenerator::init($tableName)->definition();
                $progressBar->advance();
            }
            $progressBar->finish();
        } else {
            $tables = DB::select('SHOW FULL TABLES');
            $progressBar = $output->createProgressBar(count($tables));
            foreach ($tables as $rowNumber => $table) {
                $tableData = (array) $table;
                $table = $tableData[array_key_first($tableData)];
                $tableType = $tableData['Table_type'];
                if ($tableType === 'BASE TABLE') {
                    if (in_array($table, $skippableTables)) {
                        $outputQueue[] = 'Skipped `' . $table . '` table';
                        $progressBar->advance();

                        continue;
                    }

                    $tableDefinitions[] = TableGenerator::init($table)->definition();
                    $progressBar->advance();
                } elseif ($tableType === 'VIEW') {
                    if ($skipViews || in_array($table, $skippableViews)) {
                        $outputQueue[] = 'Skipped `' . $table . '` view';
                        $progressBar->advance();

                        continue;
                    }
                    $views[] = ViewGenerator::init($table);
                    $progressBar->advance();
                } else {
                    $outputQueue[] = 'Not sure how to handle a table type of ' . $tableType . ' on row ' . $rowNumber;
                    $progressBar->advance();
                }
            }
            $progressBar->finish();
        }
        foreach ($outputQueue as $item) {
            $output->info($item);
        }

        $sorted = $this->sortTables($tableDefinitions);

        $this->writeTableMigrations($sorted, $basePath);

        foreach ($views as $view) {
            $view->write($basePath);
        }
    }
}
