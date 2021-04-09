<?php

namespace LaravelMigrationGenerator\Generators;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use LaravelMigrationGenerator\Tokenizers\MySQL\IndexTokenizer;
use LaravelMigrationGenerator\Tokenizers\MySQL\ColumnTokenizer;

class MySQLTableGenerator extends BaseTableGenerator
{
    public function __construct(string $tableName, array $rows = [])
    {
        $this->tableName = $tableName;
        $this->rows = $rows;

        if (count($this->rows) === 0) {
            $structure = DB::select('SHOW CREATE TABLE `' . $this->tableName . '`');
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

    public function cleanUp()
    {
        foreach ($this->indices as &$index) {
            $index->finalPass($this);
        }

        foreach ($this->indices as &$index) {
            if (! $index->isWritable()) {
                continue;
            }
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
    }

    public function getSchema($tab = '')
    {
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

        return $schema;
    }

    public function getIndices()
    {
        return $this->indices;
    }

    public function write(string $basePath)
    {
        if (! $this->isWritable()) {
            return;
        }
        $tab = str_repeat('    ', 3);

        $schema = $this->getSchema($tab);

        $stub = file_get_contents(__DIR__ . '/../Stubs/MigrationStub.stub');
        $stub = str_replace('[TableName]', Str::studly($this->tableName), $stub);
        $stub = str_replace('[Table]', $this->tableName, $stub);
        $stub = str_replace('[Schema]', $schema, $stub);
        file_put_contents($basePath . '/0000_00_00_000000_create_test_' . $this->tableName . '_table.php', $stub);
    }
}
