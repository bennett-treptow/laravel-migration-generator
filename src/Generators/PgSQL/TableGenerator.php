<?php

namespace BennettTreptow\LaravelMigrationGenerator\Generators\PgSQL;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use BennettTreptow\LaravelMigrationGenerator\Generators\BaseTableGenerator;
use BennettTreptow\LaravelMigrationGenerator\Tokenizers\PgSQL\IndexTokenizer;
use BennettTreptow\LaravelMigrationGenerator\Tokenizers\PgSQL\ColumnTokenizer;

/**
 * Class TableGenerator
 * @package BennettTreptow\LaravelMigrationGenerator\Generators\PgSQL
 */
class TableGenerator extends BaseTableGenerator
{
    public static function driver(): string
    {
        return 'pgsql';
    }

    public function resolveStructure()
    {

        $tableInfo = explode('.', $this->definition()->getTableName());

        $structure = (array) DB::select($this->getReverseEngineeredSql($tableInfo[0], $tableInfo[1]))[0];

        if (isset($structure['create_table'])) {
            $lines = explode("\n", $structure['create_table']);

            array_shift($lines); //get rid of first line
            array_pop($lines); //get rid of last line

            $lines = array_map(fn ($item) => trim($item), $lines);
            $this->rows = $lines;
        } else {
            $this->rows = [];
        }
    }

    protected function isColumnLine($line)
    {
        return ! Str::startsWith($line, ['KEY', 'PRIMARY', 'UNIQUE', 'FULLTEXT', 'CONSTRAINT']);
    }

    public function parse()
    {
        foreach ($this->rows as $line) {
            if ($this->isColumnLine($line)) {
                $tokenizer = ColumnTokenizer::parse($line);
                $this->definition()->addColumnDefinition($tokenizer->definition());
            } else {
                $tokenizer = IndexTokenizer::parse($line);
                $this->definition()->addIndexDefinition($tokenizer->definition());
            }
        }
    }

    // get the create table sql
    // refer to https://stackoverflow.com/a/60749494/10749312
    private function getReverseEngineeredSql($schema, $table) {

        return "SELECT 'CREATE TABLE ' || pn.nspname || '.' || pc.relname || E'(\n' ||
               string_agg(pa.attname || ' ' || pg_catalog.format_type(pa.atttypid, pa.atttypmod) || coalesce(' DEFAULT ' || (
                       SELECT pg_catalog.pg_get_expr(d.adbin, d.adrelid)
                       FROM pg_catalog.pg_attrdef d
                       WHERE d.adrelid = pa.attrelid
                         AND d.adnum = pa.attnum
                         AND pa.atthasdef
                       ),
                 '') || ' ' ||
                      CASE pa.attnotnull
                          WHEN TRUE THEN 'NOT NULL'
                          ELSE 'NULL'
                      END, E',\n') ||
           coalesce((SELECT E',\n' || string_agg('CONSTRAINT ' || pc1.conname || ' ' || pg_get_constraintdef(pc1.oid), E',\n' ORDER BY pc1.conindid)
                    FROM pg_constraint pc1
                    WHERE pc1.conrelid = pa.attrelid), '') ||
           E');' create_table
        FROM pg_catalog.pg_attribute pa
        JOIN pg_catalog.pg_class pc
            ON pc.oid = pa.attrelid
            AND pc.relname = '{$table}'
        JOIN pg_catalog.pg_namespace pn
            ON pn.oid = pc.relnamespace
            AND pn.nspname = '{$schema}'
        WHERE pa.attnum >
        0    AND NOT pa.attisdropped
        GROUP BY pn.nspname, pc.relname, pa.attrelid;";
    }
}
