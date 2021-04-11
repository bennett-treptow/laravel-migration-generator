<?php

namespace LaravelMigrationGenerator\Generators\Concerns;

use LaravelMigrationGenerator\Generators\BaseTableGenerator;

/**
 * Trait CleansUpTimestampsColumn
 * @package LaravelMigrationGenerator\Generators\Concerns
 * @mixin BaseTableGenerator
 */
trait CleansUpTimestampsColumn
{
    protected function cleanUpTimestampsColumn(): void
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
}
