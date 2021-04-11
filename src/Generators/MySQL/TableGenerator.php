<?php

namespace LaravelMigrationGenerator\Generators\MySQL;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use LaravelMigrationGenerator\Generators\BaseTableGenerator;
use LaravelMigrationGenerator\Tokenizers\MySQL\IndexTokenizer;
use LaravelMigrationGenerator\Tokenizers\MySQL\ColumnTokenizer;

/**
 * Class TableGenerator
 * @package LaravelMigrationGenerator\Generators\MySQL
 * @property IndexTokenizer[] $indices
 * @property ColumnTokenizer[] $columns
 */
class TableGenerator extends BaseTableGenerator
{
    public function __construct(string $tableName, array $rows = [])
    {
        $this->tableName = $tableName;
        $this->rows = $rows;
    }

    public static function driver(): string
    {
        return 'mysql';
    }

    public function resolveStructure()
    {
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

    public function getSchema($tab = ''): string
    {
        $schema = collect($this->columns)
            ->filter(fn ($col) => $col->isWritable())
            ->map(function ($column) use ($tab) {
                return $tab . $column->definition()->render() . ';';
            })
            ->implode("\n");

        $indices = collect($this->indices)
            ->filter(fn ($index) => $index->isWritable());

        if ($indices->count() > 0) {
            $schema .= "\n";
            $schema .= $indices
                ->map(function ($index) use ($tab) {
                    return $tab . $index->definition()->render() . ';';
                })
                ->implode("\n");
        }

        return $schema;
    }
}
