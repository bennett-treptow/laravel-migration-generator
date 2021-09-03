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
        $this->assertEquals(['tests.id', 'test_items.id'], $order[0]);
        $this->assertEmpty($order[1]);
    }

    public function test_it_finds_cyclical_dependencies()
    {
        $tableDefinition = new TableDefinition([
            'tableName'         => 'tests',
            'columnDefinitions' => [
                (new ColumnDefinition())->setColumnName('id')->setMethodName('id')->setAutoIncrementing(true)->setPrimary(true),
                (new ColumnDefinition())->setColumnName('test_item_id')->setMethodName('bigInteger')->setNullable(false)->setUnsigned(true),
            ],
            'indexDefinitions' => [
                (new IndexDefinition())->setIndexName('fk_test_item_id')->setIndexType('foreign')->setForeignReferencedColumns(['id'])->setForeignReferencedTable('test_items')
            ],
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
        $this->assertEquals([], $order[0]);
        $this->assertEquals(['tests.id', 'test_items.id'], $order[1][0]);
        $this->assertEquals(['test_items.id', 'tests.id'], $order[1][1]);
    }
}
