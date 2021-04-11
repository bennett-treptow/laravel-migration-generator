<?php

namespace Tests\Unit\Generators;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use LaravelMigrationGenerator\Generators\MySQL\TableGenerator;

class MySQLTableGeneratorTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        $path = __DIR__ . '/../../migrations';
        $this->cleanUpMigrations($path);
    }

    private function assertSchemaHas($str, $schema)
    {
        $this->assertStringContainsString($str, $schema);
    }

    public function test_runs_correctly()
    {
        $generator = TableGenerator::init('table', [
            '`id` int(9) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            '`user_id` int(9) unsigned NOT NULL',
            '`note` varchar(255) NOT NULL',
            'KEY `fk_user_id_idx` (`user_id`)',
            'CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]);

        $schema = $generator->getSchema();
        $this->assertSchemaHas('$table->increments(\'id\');', $schema);
        $this->assertSchemaHas('$table->integer(\'user_id\')->unsigned();', $schema);
        $this->assertSchemaHas('$table->string(\'note\');', $schema);
        $this->assertSchemaHas('$table->foreign(\'user_id\', \'fk_user_id\')->references(\'id\')->on(\'users\')->onDelete(\'cascade\')->onUpdate(\'cascade\');', $schema);
    }

    private function cleanUpMigrations($path)
    {
        if (is_dir($path)) {
            foreach (glob($path . '/*.php') as $file) {
                unlink($file);
            }
            rmdir($path);
        }
    }

    public function test_writes()
    {
        Config::set('laravel-migration-generator.table_naming_scheme', '0000_00_00_000000_create_[TableName]_table.php');
        $generator = TableGenerator::init('table', [
            '`id` int(9) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            '`user_id` int(9) unsigned NOT NULL',
            '`note` varchar(255) NOT NULL',
            'KEY `fk_user_id_idx` (`user_id`)',
            'CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]);

        $path = __DIR__ . '/../../migrations';

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $generator->write($path);

        $this->assertFileExists($path . '/0000_00_00_000000_create_table_table.php');
    }
}
