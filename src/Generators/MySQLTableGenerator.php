<?php

namespace LaravelMigrationGenerator\Generators;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LaravelMigrationGenerator\Tokenizers\MySQL\ColumnTokenizer;
use LaravelMigrationGenerator\Tokenizers\MySQL\IndexTokenizer;

class MySQLTableGenerator extends BaseTableGenerator
{
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;

        $structure = DB::select('SHOW CREATE TABLE `' . $tableName . '`');
        $structure = $structure[0];
        $structure = (array) $structure;
        if (isset($structure['Create Table'])) {
            $lines = explode("\n", $structure['Create Table']);

            array_shift($lines); //get rid of first line
            array_pop($lines); //get rid of last line

            $lines = array_map(fn ($item) => trim($item), $lines);
            $this->rows = $lines;
        } else {
            //might be a view
            $this->markAsWritable(false);
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
                $this->columns[] = $tokenizer;
            } else {
                $tokenizer = IndexTokenizer::parse($line);
                $this->indices[] = $tokenizer;
            }
        }
    }

    public function finalPass()
    {
        foreach ($this->indices as &$index) {
            $columns = $index->getIndexColumns();

            foreach ($columns as $indexColumn) {
                foreach ($this->columns as $column) {
                    if ($column->getColumnName() === $indexColumn) {
                        $column->index($index);
                    }
                }
            }
        }
        foreach ($this->columns as &$column) {
            $column->finalPass($this);
        }

        foreach ($this->indices as &$index) {
            $index->finalPass($this);
        }
    }

    public function write(string $basePath)
    {
        if (! $this->isWritable()) {
            return;
        }
        $tab = str_repeat('    ', 3);
        $schema = collect($this->columns)
            ->filter(fn ($col) => $col->isWritable())
            ->map(function ($column) use ($tab) {
                return $tab . $column->toMethod() . ';';
            })
            ->implode("\n");

        $schema .= "\n";

        $schema .= collect($this->indices)
            ->filter(fn ($index) => $index->isWritable())
            ->map(function ($index) use ($tab) {
                return $tab . $index->toMethod() . ';';
            })
            ->implode("\n");

        $stub = file_get_contents(__DIR__ . '/../../Stubs/MigrationStub.stub');
        $stub = str_replace('[TableName]', Str::studly($this->tableName), $stub);
        $stub = str_replace('[Table]', $this->tableName, $stub);
        $stub = str_replace('[Schema]', $schema, $stub);
        file_put_contents($basePath.'/0000_00_00_000000_create_test_' . $this->tableName . '_table.php', $stub);
    }
}
