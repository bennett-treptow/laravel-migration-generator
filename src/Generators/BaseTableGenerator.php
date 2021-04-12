<?php

namespace LaravelMigrationGenerator\Generators;

use LaravelMigrationGenerator\Helpers\WritableTrait;
use LaravelMigrationGenerator\Generators\Concerns\WritesTablesToFile;
use LaravelMigrationGenerator\Generators\Concerns\CleansUpMorphColumns;
use LaravelMigrationGenerator\Generators\Concerns\CleansUpColumnIndices;
use LaravelMigrationGenerator\Generators\Concerns\CleansUpTimestampsColumn;
use LaravelMigrationGenerator\Generators\Concerns\CleansUpForeignKeyIndices;
use LaravelMigrationGenerator\Generators\Interfaces\TableGeneratorInterface;
use LaravelMigrationGenerator\Tokenizers\Interfaces\IndexTokenizerInterface;
use LaravelMigrationGenerator\Tokenizers\Interfaces\ColumnTokenizerInterface;

abstract class BaseTableGenerator implements TableGeneratorInterface
{
    use WritableTrait;
    use WritesTablesToFile;
    use CleansUpForeignKeyIndices;
    use CleansUpMorphColumns;
    use CleansUpTimestampsColumn;
    use CleansUpColumnIndices;

    protected string $tableName;

    protected array $rows = [];

    /** @var ColumnTokenizerInterface[] */
    protected array $columns = [];

    /** @var IndexTokenizerInterface[] */
    protected array $indices = [];

    abstract public function getSchema($tab = ''): string;

    abstract public function resolveStructure();

    abstract public function parse();

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

    public function cleanUp(): void
    {
        $this->cleanUpForeignKeyIndices();

        $this->cleanUpMorphColumns();

        $this->cleanUpTimestampsColumn();

        $this->cleanUpColumnsWithIndices();
    }

    public function getIndices(): array
    {
        return $this->indices;
    }
}
