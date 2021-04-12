<?php

namespace LaravelMigrationGenerator\Generators\Concerns;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Generators\BaseTableGenerator;
use LaravelMigrationGenerator\Tokenizers\Interfaces\ColumnTokenizerInterface;

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
                /** @var ColumnTokenizerInterface $idField */
                $idField = $fields['id'];
                /** @var ColumnTokenizerInterface $typeField */
                $typeField = $fields['type'];

                if (! ($idField->definition()->isUUID() || Str::contains($idField->definition()->getMethodName(), 'integer'))) {
                    //should only be a uuid field or integer
                    continue;
                }
                if ($typeField->definition()->getMethodName() != 'string') {
                    //should only be a string field
                    continue;
                }

                if ($idField->definition()->isUUID()) {
                    //UUID morph
                    $idField->definition()
                        ->setMethodName('uuidMorphs')
                        ->setMethodParameters([])
                        ->setColumnName($columnName);
                } else {
                    //regular morph
                    $idField->definition()
                        ->setMethodName('morphs')
                        ->setColumnName($columnName);
                }
                $typeField->markAsWritable(false);

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
