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
                            $column->definition()->setPrimary(true)->addIndexDefinition($index->definition());
                            $index->markAsWritable(false);
                        } elseif ($indexType === 'index' && ! $isMultiColumnIndex) {
                            $isForeignKeyIndex = false;
                            foreach ($this->indices as $innerIndex) {
                                if ($innerIndex->definition()->getIndexType() === 'foreign' && ! $innerIndex->definition()->isMultiColumnIndex() && $innerIndex->definition()->getIndexColumns()[0] == $column->definition()->getColumnName()) {
                                    $isForeignKeyIndex = true;

                                    break;
                                }
                            }
                            if ($isForeignKeyIndex === false) {
                                $column->definition()->setIndex(true)->addIndexDefinition($index->definition());
                            }
                            $index->markAsWritable(false);
                        } elseif ($indexType === 'unique' && ! $isMultiColumnIndex) {
                            $column->definition()->setUnique(true)->addIndexDefinition($index->definition());
                            $index->markAsWritable(false);
                        }
                    }
                }
            }
        }
    }
}
