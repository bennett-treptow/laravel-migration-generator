<?php

namespace BennettTreptow\LaravelMigrationGenerator\GeneratorManagers;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use BennettTreptow\LaravelMigrationGenerator\Generators\PgSQL\TableGenerator;
use BennettTreptow\LaravelMigrationGenerator\GeneratorManagers\Interfaces\GeneratorManagerInterface;

class PgSQLGeneratorManager extends BaseGeneratorManager implements GeneratorManagerInterface
{
    public static function driver(): string
    {
        return 'pgsql';
    }

    public function init()
    {
        $tables = DB::select('SELECT table_schema, table_name FROM information_schema.tables order by 1;');

        foreach ($tables as $table) {

            if(in_array($table->table_schema, $this->getPgSqlInternalTables)) {
                continue;
            }
            $schemaInfo[$table->table_schema][] = $table->table_name;

            // TODO: add table view generator

            $this->addTableDefinition(TableGenerator::init($table->table_schema . '.'. $table->table_name)->definition());
        }
    }

    private  $getPgSqlInternalTables =  [
        'pg_toast',
        'pg_temp_1',
        'pg_toast_temp_1',
        'pg_catalog',
        'information_schema',
    ];

}
