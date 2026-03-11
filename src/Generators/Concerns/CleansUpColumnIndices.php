<?php

namespace LaravelMigrationGenerator\Generators\Concerns;

use LaravelMigrationGenerator\Definitions\IndexDefinition;
use LaravelMigrationGenerator\Generators\BaseTableGenerator;

/**
 * Trait CleansUpColumnIndices
 *
 * @mixin BaseTableGenerator
 */
trait CleansUpColumnIndices
{
    protected function cleanUpColumnsWithIndices(): void
    {
        foreach ($this->definition()->getIndexDefinitions() as &$index) {
            /** @var IndexDefinition $index */
            if (! $index->isWritable()) {
                continue;
            }
            $columns = $index->getIndexColumns();

            foreach ($columns as $indexColumn) {
                foreach ($this->definition()->getColumnDefinitions() as $column) {
                    if ($column->getColumnName() === $indexColumn) {
                        $indexType = $index->getIndexType();
                        $isMultiColumnIndex = $index->isMultiColumnIndex();

                        if ($indexType === 'primary' && ! $isMultiColumnIndex) {
                            $column->setPrimary(true)->addIndexDefinition($index);
                            $index->markAsWritable(false);
                        } elseif ($indexType === 'index' && ! $isMultiColumnIndex) {
                            $isForeignKeyIndex = false;
                            foreach ($this->definition()->getIndexDefinitions() as $innerIndex) {
                                $innerIndexColumns = $innerIndex->getIndexColumns();
                                if ($innerIndex->getIndexType() === 'foreign' && ! $innerIndex->isMultiColumnIndex() && ! empty($innerIndexColumns) && $innerIndexColumns[0] == $column->getColumnName()) {
                                    $isForeignKeyIndex = true;

                                    break;
                                }
                            }
                            if ($isForeignKeyIndex === false) {
                                $column->setIndex(true)->addIndexDefinition($index);
                            }
                            $index->markAsWritable(false);
                        } elseif ($indexType === 'unique' && ! $isMultiColumnIndex) {
                            $column->setUnique(true)->addIndexDefinition($index);
                            $index->markAsWritable(false);
                        }
                    }
                }
            }
        }
    }
}
