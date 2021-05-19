<?php

namespace LaravelMigrationGenerator\Generators\MySQL;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use LaravelMigrationGenerator\Definitions\IndexDefinition;
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

    public static function sort(Collection $generators): Collection
    {
        $keyedGenerators = collect($generators->toArray())
            ->keyBy(function (TableGenerator $tableGenerator) {
                return $tableGenerator->tableName;
            });

        $source = $keyedGenerators
            ->map(function (TableGenerator $tableGenerator) {
                return collect($tableGenerator->indices)
                    ->filter(function (IndexTokenizer $indexTokenizer) {
                        return $indexTokenizer->definition()->getIndexType() === 'foreign';
                    })
                    ->map(function (IndexTokenizer $indexTokenizer) {
                        return $indexTokenizer->definition()->getForeignReferencedTable();
                    })
                    ->filter(function (string $tableName) use ($tableGenerator) {
                        return $tableName !== $tableGenerator->tableName;
                    })
                    ->unique()
                    ->values()
                    ->toArray();
            });

        // Detect and track cross dependencies between tables
        // TODO - better solution is to remove circularity by pushing
        //        an FK only table modification into its own migration
        //        that runs after both tables exist
        $circularities = $source->reduce(function (Collection $carry, array $sourceDependencies, string $sourceTableName) use ($source) {
            $circularities = collect($sourceDependencies)
                ->filter(function ($dependencyTableName) use ($sourceTableName, $source) {
                    return in_array($sourceTableName, $source->get($dependencyTableName));
                })
                ->values()
                ->toArray();

            if ($circularities) {
                $carry->put($sourceTableName, $circularities);
            }

            return $carry;
        }, collect());

        // TODO - config setting to allow automatic resolution?

        // Reduce to table names only
        $circularities = $circularities->keys();

        $sortedTables = [];
        while ($source->isNotEmpty()) {
            $sortedCount = count($sortedTables);

            $source
                ->each(function (array $dependencies, string $tableName) use (&$sortedTables, $circularities) {
                    if (!$dependencies || !array_diff($dependencies, $sortedTables)) {
                        $sortedTables[] = $tableName;
                    }
                });

            // Check if stuck in circularity
            if (count($sortedTables) === $sortedCount) {
                if ($circularities->isNotEmpty()) {
                    $sortedTables[] = $circularities->pop();
                } else {
                    throw new \LogicException('Cannot resolve table sort.');
                }
            }

            $source = $source->filter(function (array $dependencies, string $tableName) use ($sortedTables) {
                return !in_array($tableName, $sortedTables);
            });
        }

        $result = collect();
        foreach ($sortedTables as $tableName) {
            $result->push($keyedGenerators->get($tableName));
        }

        return $result;
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
