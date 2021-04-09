<?php

namespace LaravelMigrationGenerator\GeneratorManagers;

use App\Services\ViewTokenizer;
use Illuminate\Support\Facades\DB;
use LaravelMigrationGenerator\Generators\MySQLTableGenerator;

class MySQLGeneratorManager implements GeneratorManagerInterface
{
    public function handle(string $basePath, ?string $singleTable = null)
    {
        if ($singleTable === null) {
            $tables = DB::select('SHOW FULL TABLES');
            $skippableTables = [
                'migrations'
            ];

            foreach ($tables as $table) {
                $tableData = (array) $table;
                $table = $tableData[array_key_first($tableData)];
                $tableType = $tableData['Table_type'];
                if ($tableType === 'BASE TABLE') {
                    if (in_array($table, $skippableTables)) {
                        continue;
                    }

                    $generator = MySQLTableGenerator::init($table);
                    $generator->write($basePath);
                } elseif ($tableType === 'VIEW') {
                    $generator = ViewTokenizer::init($table);
                    $generator->write($basePath);
                }
            }
        } else {
            $generator = MySQLTableGenerator::init($singleTable);
            $generator->write($basePath);
        }
    }
}
