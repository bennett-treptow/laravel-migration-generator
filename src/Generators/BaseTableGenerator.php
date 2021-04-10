<?php

namespace LaravelMigrationGenerator\Generators;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Tokenizers\Traits\WritableTokenizer;
use LaravelMigrationGenerator\Generators\Interfaces\TableGeneratorInterface;
use LaravelMigrationGenerator\Tokenizers\Interfaces\IndexTokenizerInterface;
use LaravelMigrationGenerator\Tokenizers\Interfaces\ColumnTokenizerInterface;

abstract class BaseTableGenerator implements TableGeneratorInterface
{
    use WritableTokenizer;

    protected $tableName;

    protected $rows;

    /** @var ColumnTokenizerInterface[] */
    protected $columns = [];

    /** @var IndexTokenizerInterface[] */
    protected $indices = [];

    public static function init(string $tableName, array $rows = [])
    {
        $instance = (new static($tableName, $rows));

        if ($instance->shouldResolveStructure()) {
            $instance->resolveStructure();
        }

        $instance->parse();
        $instance->cleanUp();

        return $instance;
    }

    public function shouldResolveStructure(): bool
    {
        return count($this->rows) === 0;
    }

    public function cleanUp()
    {
        $this->findForeignKeyIndices();

        $this->findMorphColumns();

        $this->findTimestampsColumn();

        $this->findColumnsWithIndices();
    }

    protected function findForeignKeyIndices()
    {
        foreach ($this->indices as $index) {
            if ($index->definition()->getIndexType() === 'index') {
                //look for corresponding foreign key for this index
                $columns = $index->definition()->getIndexColumns();
                $indexName = $index->definition()->getIndexName();

                foreach ($this->indices as $innerIndex) {
                    if ($innerIndex->definition()->getIndexName() !== $indexName) {
                        if ($innerIndex->definition()->getIndexType() === 'foreign') {
                            $cols = $innerIndex->definition()->getIndexColumns();
                            if (count(array_intersect($columns, $cols)) === count($columns)) {
                                //has same columns
                                $index->markAsWritable(false);

                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    protected function findMorphColumns()
    {
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
    }

    protected function findColumnsWithIndices()
    {
        foreach ($this->indices as &$index) {
            if (! $index->isWritable()) {
                continue;
            }
            $columns = $index->definition()->getIndexColumns();

            foreach ($columns as $indexColumn) {
                foreach ($this->columns as $column) {
                    if ($column->definition()->getColumnName() === $indexColumn) {
                        $indexType = $index->definition()->getIndexType();
                        $isMultiColumnIndex = $index->definition()->isMultiColumnIndex();

                        if ($indexType === 'primary' && ! $isMultiColumnIndex) {
                            $column->definition()->setPrimary(true);
                            $index->markAsWritable(false);
                        } elseif ($indexType === 'index' && ! $isMultiColumnIndex) {
                            $column->definition()->setIndex(true);
                            $index->markAsWritable(false);
                        } elseif ($indexType === 'unique' && ! $isMultiColumnIndex) {
                            $column->definition()->setUnique(true);
                            $index->markAsWritable(false);
                        }
                    }
                }
            }
        }
    }

    protected function findTimestampsColumn()
    {
        $timestampColumns = [];
        foreach ($this->columns as &$column) {
            $columnName = $column->definition()->getColumnName();
            if ($columnName === 'created_at') {
                $timestampColumns['created_at'] = $column;
            } elseif ($columnName === 'updated_at') {
                $timestampColumns['updated_at'] = $column;
            }
            if (count($timestampColumns) === 2) {
                $timestampColumns['created_at']->definition()
                    ->setColumnName(null)
                    ->setMethodName('timestamps')
                    ->setNullable(false);
                $timestampColumns['updated_at']->markAsWritable(false);

                break;
            }
        }
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

    abstract public function getSchema($tab = ''): string;

    abstract public function resolveStructure();

    abstract public function parse();
}
