<?php

namespace LaravelMigrationGenerator\Generators\Concerns;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Generators\BaseTableGenerator;

/**
 * Trait CleansUpMorphColumns
 * @package LaravelMigrationGenerator\Generators\Concerns
 * @mixin BaseTableGenerator
 */
trait CleansUpMorphColumns
{
    protected function cleanUpMorphColumns(): void
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
}
