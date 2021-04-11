<?php

namespace LaravelMigrationGenerator\Generators\Concerns;

use LaravelMigrationGenerator\Generators\BaseTableGenerator;

/**
 * Trait CleansUpForeignKeyIndices
 * @package LaravelMigrationGenerator\Generators\Concerns
 * @mixin BaseTableGenerator
 */
trait CleansUpForeignKeyIndices
{
    protected function cleanUpForeignKeyIndices(): void
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
}
