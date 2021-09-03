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
        $this->assertEquals(['tests', 'test_items'], array_keys($order['nonCircular']));
        $this->assertEmpty($order['circular']);
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
                (new IndexDefinition())->setIndexName('fk_test_item_id')->setIndexColumns(['test_item_id'])->setIndexType('foreign')->setForeignReferencedColumns(['id'])->setForeignReferencedTable('test_items')
            ],
        ]);

        $foreignTableDefinition = new TableDefinition([
            'tableName'         => 'test_items',
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

        $this->assertEquals([], $order['nonCircular']);
        $this->assertCount(2, $order['circular']);
        $this->assertEquals(['tests', 'test_items'], array_keys($order['circular'][0]));
        $this->assertEquals(['test_items', 'tests'], array_keys($order['circular'][1]));

        $testsDependency = $order['circular'][0]['tests'];
        $testsDependency->assertHasDependentTable('test_items');
        $testsDependency->assertHasDependencyRelation('id', 'test_items', 'test_id');

        $testItemsDependency = $order['circular'][0]['test_items'];
        $testItemsDependency->assertHasDependentTable('tests');
        $testItemsDependency->assertHasDependencyRelation('id', 'tests', 'test_item_id');
    }
}
