<?php

namespace Tests\Unit;

use Tests\TestCase;
use LaravelMigrationGenerator\Helpers\DependencyResolver;
use LaravelMigrationGenerator\Definitions\IndexDefinition;
use LaravelMigrationGenerator\Definitions\TableDefinition;
use LaravelMigrationGenerator\Definitions\ColumnDefinition;

class DependencyResolverTest extends TestCase
{
    public function test_it_can_find_simple_dependencies()
    {
        $tableDefinition = new TableDefinition([
            'tableName'         => 'tests',
            'columnDefinitions' => [
                (new ColumnDefinition())->setColumnName('id')->setMethodName('id')->setAutoIncrementing(true)->setPrimary(true),
                (new ColumnDefinition())->setColumnName('name')->setMethodName('string')->setNullable(false),
            ],
            'indexDefinitions' => [],
        ]);

        $foreignTableDefinition = new TableDefinition([
            'tableName'         => 'test_items',
            'columnDefinitions' => [
                (new ColumnDefinition())->setColumnName('id')->setMethodName('id')->setAutoIncrementing(true)->setPrimary(true),
                (new ColumnDefinition())->setColumnName('test_id')->setMethodName('bigInteger')->setNullable(false)->setUnsigned(true),
            ],
            'indexDefinitions' => [
                (new IndexDefinition())->setIndexName('fk_test_id')->setIndexType('foreign')->setForeignReferencedColumns(['id'])->setForeignReferencedTable('tests')
            ],
        ]);

        $resolver = new DependencyResolver([$tableDefinition, $foreignTableDefinition]);
        $order = $resolver->getDependencyOrder();
        $this->assertCount(2, $order);
        $this->assertEquals('tests', $order[0]->getTableName());
        $this->assertEquals('test_items', $order[1]->getTableName());
    }

    public function test_it_finds_cyclical_dependencies()
    {
        $tableDefinition = new TableDefinition([
            'tableName'         => 'tests',
            'driver'            => 'mysql',
            'columnDefinitions' => [
                (new ColumnDefinition())->setColumnName('id')->setMethodName('id')->setAutoIncrementing(true)->setPrimary(true),
                (new ColumnDefinition())->setColumnName('test_item_id')->setMethodName('bigInteger')->setNullable(false)->setUnsigned(true),
            ],
            'indexDefinitions' => [
                (new IndexDefinition())->setIndexName('fk_test_item_id')->setIndexColumns(['test_item_id'])->setIndexType('foreign')->setForeignReferencedColumns(['id'])->setForeignReferencedTable('test_items')
            ],
        ]);

        $foreignTableDefinition = new TableDefinition([
            'tableName'         => 'test_items',
            'driver'            => 'mysql',
            'columnDefinitions' => [
                (new ColumnDefinition())->setColumnName('id')->setMethodName('id')->setAutoIncrementing(true)->setPrimary(true),
                (new ColumnDefinition())->setColumnName('test_id')->setMethodName('bigInteger')->setNullable(false)->setUnsigned(true),
            ],
            'indexDefinitions' => [
                (new IndexDefinition())->setIndexName('fk_test_id')->setIndexColumns(['test_id'])->setIndexType('foreign')->setForeignReferencedColumns(['id'])->setForeignReferencedTable('tests')
            ],
        ]);

        $resolver = new DependencyResolver([$tableDefinition, $foreignTableDefinition]);

        $order = $resolver->getDependencyOrder();
        $this->assertCount(4, $order);
        $this->assertEquals('$table->foreign(\'test_id\', \'fk_test_id\')->references(\'id\')->on(\'tests\');', $order[2]->formatter()->getSchema());
        $this->assertEquals('$table->foreign(\'test_item_id\', \'fk_test_item_id\')->references(\'id\')->on(\'test_items\');', $order[3]->formatter()->getSchema());
    }
}
