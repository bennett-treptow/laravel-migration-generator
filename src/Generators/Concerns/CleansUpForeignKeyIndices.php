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
        $indexDefinitions = $this->definition()->getIndexDefinitions();
        foreach ($indexDefinitions as $index) {
            /** @var \LaravelMigrationGenerator\Definitions\IndexDefinition $index */
            if ($index->getIndexType() === 'index') {
                //look for corresponding foreign key for this index
                $columns = $index->getIndexColumns();
                $indexName = $index->getIndexName();

                foreach ($indexDefinitions as $innerIndex) {
                    /** @var \LaravelMigrationGenerator\Definitions\IndexDefinition $innerIndex */
                    if ($innerIndex->getIndexName() !== $indexName) {
                        if ($innerIndex->getIndexType() === 'foreign') {
                            $cols = $innerIndex->getIndexColumns();
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
