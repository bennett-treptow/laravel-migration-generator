<?php

namespace Tests\Unit\GeneratorManagers;

use Tests\TestCase;
use Mockery\MockInterface;
use Illuminate\Support\Facades\DB;
use LaravelMigrationGenerator\Definitions\IndexDefinition;
use LaravelMigrationGenerator\Definitions\TableDefinition;
use LaravelMigrationGenerator\Definitions\ColumnDefinition;
use LaravelMigrationGenerator\GeneratorManagers\MySQLGeneratorManager;

class MySQLGeneratorManagerTest extends TestCase
{
    protected function getManagerMock(array $tableDefinitions)
    {
        return $this->partialMock(MySQLGeneratorManager::class, function (MockInterface $mock) use ($tableDefinitions) {
            $mock->shouldReceive('init', 'createMissingDirectory', 'writeTableMigrations', 'writeViewMigrations');
            $mock->shouldReceive('createMissingDirectory');

            $mock->shouldReceive('getTableDefinitions')->andReturn($tableDefinitions);
        });
    }

    public function test_can_sort_tables()
    {
        /** @var MySQLGeneratorManager $mocked */
        $mocked = $this->getManagerMock([
            new TableDefinition([
                'tableName'         => 'tests',
                'driver'            => 'mysql',
                'columnDefinitions' => [
                    (new ColumnDefinition())->setColumnName('id')->setMethodName('id')->setAutoIncrementing(true)->setPrimary(true),
                    (new ColumnDefinition())->setColumnName('test_item_id')->setMethodName('bigInteger')->setNullable(false)->setUnsigned(true),
                ],
                'indexDefinitions' => [
                    (new IndexDefinition())->setIndexName('fk_test_item_id')->setIndexColumns(['test_item_id'])->setIndexType('foreign')->setForeignReferencedColumns(['id'])->setForeignReferencedTable('test_items')
                ],
            ]),
            new TableDefinition([
                'tableName'         => 'test_items',
                'driver'            => 'mysql',
                'columnDefinitions' => [
                    (new ColumnDefinition())->setColumnName('id')->setMethodName('id')->setAutoIncrementing(true)->setPrimary(true),
                    (new ColumnDefinition())->setColumnName('test_id')->setMethodName('bigInteger')->setNullable(false)->setUnsigned(true),
                ],
                'indexDefinitions' => [
                    (new IndexDefinition())->setIndexName('fk_test_id')->setIndexColumns(['test_id'])->setIndexType('foreign')->setForeignReferencedColumns(['id'])->setForeignReferencedTable('tests')
                ],
            ])
        ]);
        $sorted = $mocked->sortTables($mocked->getTableDefinitions());
        $this->assertCount(4, $sorted);
        $this->assertStringContainsString('$table->dropForeign', $sorted[3]->formatter()->stubTableDown());
    }

    public function test_can_remove_database_prefix()
    {
        $connection = DB::getDefaultConnection();
        config()->set('database.connections.' . $connection . '.prefix', 'wp_');

        $mocked = $this->partialMock(MySQLGeneratorManager::class, function (MockInterface $mock) {
            $mock->shouldReceive('init');
        });

        $definition = (new TableDefinition())->setTableName('wp_posts');
        $mocked->addTableDefinition($definition);
        $this->assertEquals('posts', $definition->getTableName());

        $definition = (new TableDefinition())->setTableName('posts');
        $mocked->addTableDefinition($definition);
        $this->assertEquals('posts', $definition->getTableName());


        config()->set('database.connections.' . $connection . '.prefix', '');

        $definition = (new TableDefinition())->setTableName('wp_posts');
        $mocked->addTableDefinition($definition);
        $this->assertEquals('wp_posts', $definition->getTableName());

        $definition = (new TableDefinition())->setTableName('posts');
        $mocked->addTableDefinition($definition);
        $this->assertEquals('posts', $definition->getTableName());
    }
}
