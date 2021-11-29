<?php

namespace Tests\Unit;

use Tests\TestCase;
use LaravelMigrationGenerator\Definitions\IndexDefinition;
use LaravelMigrationGenerator\Definitions\ColumnDefinition;

class ColumnDefinitionTest extends TestCase
{
    public function test_it_can_add_index_definitions()
    {
        $columnDefinition = (new ColumnDefinition())->setIndex(true)->setColumnName('testing')->setMethodName('string');
        $indexDefinition = (new IndexDefinition())->setIndexName('test')->setIndexType('index');
        $columnDefinition->addIndexDefinition($indexDefinition);

        $this->assertEquals('$table->string(\'testing\')->index(\'test\')', $columnDefinition->render());
    }

    public function test_it_prunes_empty_primary_key_index()
    {
        $columnDefinition = (new ColumnDefinition())
            ->setPrimary(true)
            ->setColumnName('testing')
            ->setUnsigned(true)
            ->setMethodName('integer');
        $indexDefinition = (new IndexDefinition())
            ->setIndexType('primary');
        $columnDefinition->addIndexDefinition($indexDefinition);

        $this->assertEquals('$table->unsignedInteger(\'testing\')->primary()', $columnDefinition->render());
    }
}
