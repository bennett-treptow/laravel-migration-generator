<?php

namespace LaravelMigrationGenerator\Generators\Concerns;

use LaravelMigrationGenerator\Generators\BaseTableGenerator;

/**
 * Trait CleansUpColumnIndices
 * @package LaravelMigrationGenerator\Generators\Concerns
 * @mixin BaseTableGenerator
 */
trait CleansUpColumnIndices
{
    protected function cleanUpColumnsWithIndices(): void
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
}
