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

    public function cleanUp()
    {
        foreach ($this->indices as &$index) {
            $index->finalPass($this);
        }

        foreach ($this->indices as &$index) {
            if (! $index->isWritable()) {
                continue;
            }
            $columns = $index->definition()->getIndexColumns();

            foreach ($columns as $indexColumn) {
                foreach ($this->columns as $column) {
                    if ($column->definition()->getColumnName() === $indexColumn) {
                        $column->index($index);
                    }
                }
            }
        }
        $morphColumns = [];

        foreach ($this->columns as &$column) {
            if (Str::endsWith($columnName = $column->definition()->getColumnName(), ['_id', '_type'])) {
                $pieces = explode('_', $columnName);
                $type = array_pop($pieces); //pop off id or type
                $morphColumn = implode('_', $pieces);
                $morphColumns[$morphColumn][$type] = $column;
            }
        }

        foreach ($morphColumns as $columnName => $fields) {
            if (count($fields) === 2) {
                $fields['id']->definition()
                    ->setMethodName('morphs')
                    ->setColumnName($columnName);
                $fields['type']->markAsWritable(false);

                foreach ($this->indices as $index) {
                    $columns = $index->definition()->getIndexColumns();
                    $morphColumns = [$columnName . '_id', $columnName . '_type'];

                    if (count($columns) == count($morphColumns) && array_diff($columns, $morphColumns) === array_diff($morphColumns, $columns)) {
                        $index->markAsWritable(false);

                        break;
                    }
                }
            }
        }

        $timestampColumns = [];
        foreach($this->columns as &$column){
            $columnName = $column->definition()->getColumnName();
            if($columnName === 'created_at'){
                $timestampColumns['created_at'] = $column;
            } elseif($columnName === 'updated_at'){
                $timestampColumns['updated_at'] = $column;
            }
            if(count($timestampColumns) === 2){
                $timestampColumns['created_at']->definition()
                    ->setColumnName(null)
                    ->setMethodName('timestamps')
                    ->setNullable(false);
                $timestampColumns['updated_at']->markAsWritable(false);
                break;
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

    public function write(string $basePath)
    {
        if (! $this->isWritable()) {
            return;
        }
        $tab = str_repeat('    ', 3);

        $schema = $this->getSchema($tab);

        $stub = file_get_contents(__DIR__ . '/../../Stubs/MigrationStub.stub');
        $stub = str_replace('[TableName]', Str::studly($this->tableName), $stub);
        $stub = str_replace('[Table]', $this->tableName, $stub);
        $stub = str_replace('[Schema]', $schema, $stub);
        file_put_contents($basePath . '/0000_00_00_000000_create_test_' . $this->tableName . '_table.php', $stub);
    }
}
