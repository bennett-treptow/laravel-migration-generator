<?php

namespace LaravelMigrationGenerator\GeneratorManagers;

use Illuminate\Support\Facades\DB;
use LaravelMigrationGenerator\Generators\MySQL\ViewGenerator;
use LaravelMigrationGenerator\Generators\MySQL\TableGenerator;
use LaravelMigrationGenerator\GeneratorManagers\Interfaces\GeneratorManagerInterface;

class MySQLGeneratorManager extends BaseGeneratorManager implements GeneratorManagerInterface
{
    public function handle(string $basePath, ?string $singleTable = null)
    {
        $this->createMissingDirectory($basePath);

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

                    $generator = TableGenerator::init($table);
                    $generator->write($basePath);
                } elseif ($tableType === 'VIEW') {
                    $generator = ViewGenerator::init($table);
                    $generator->write($basePath);
                }
            }
        } else {
            $generator = TableGenerator::init($singleTable);
            $generator->write($basePath);
        }
    }
}
