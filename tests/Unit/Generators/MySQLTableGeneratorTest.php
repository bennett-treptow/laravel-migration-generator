<?php

namespace Tests\Unit\Generators;

use Tests\TestCase;
use LaravelMigrationGenerator\Generators\MySQLTableGenerator;

class MySQLTableGeneratorTest extends TestCase
{
    private function assertSchemaHas($str, $schema)
    {
        $this->assertStringContainsString($str, $schema);
    }

    public function test_runs_correctly()
    {
        $generator = new MySQLTableGenerator('table', [
            '`id` int(9) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            '`user_id` int(9) unsigned NOT NULL',
            '`note` varchar(255) NOT NULL',
            'KEY `fk_user_id_idx` (`user_id`)',
            'CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]);

        $generator->parse();
        $generator->cleanUp();
        $schema = $generator->getSchema();
        $this->assertSchemaHas('$table->increments(\'id\');', $schema);
        $this->assertSchemaHas('$table->unsignedInteger(\'user_id\', 9);', $schema);
        $this->assertSchemaHas('$table->string(\'note\', 255);', $schema);
        $this->assertSchemaHas('$table->foreign(\'user_id\', \'fk_user_id\')->references(\'id\')->on(\'users\')->onDelete(\'cascade\')->onUpdate(\'cascade\');', $schema);
    }

    public function test_works_with_morph_columns(){
        $structure = ['`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT',
          '`business_id` int(10) unsigned NOT NULL',
          '`trigger` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL',
          '`trigger_at` timestamp NULL DEFAULT NULL',
          '`assignable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL',
         '`assignable_id` bigint(20) unsigned DEFAULT NULL',
          '`action_template_id` bigint(20) unsigned DEFAULT NULL',
          '`delay_interval` int(10) unsigned NOT NULL DEFAULT \'0\'',
          '`due_interval` int(10) unsigned NOT NULL DEFAULT \'0\'',
          '`created_at` timestamp NULL DEFAULT NULL',
          '`updated_at` timestamp NULL DEFAULT NULL',
          '`deleted_at` timestamp NULL DEFAULT NULL',
          'PRIMARY KEY (`id`)',
          'KEY `action_automations_assignable_type_assignable_id_index` (`assignable_type`,`assignable_id`)',
          'KEY `action_automations_business_id_foreign` (`business_id`)',
          'KEY `action_automations_action_template_id_foreign` (`action_template_id`)',
          'CONSTRAINT `action_automations_action_template_id_foreign` FOREIGN KEY (`action_template_id`) REFERENCES `action_templates` (`id`)',
          'CONSTRAINT `action_automations_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`)'
        ];

        $generator = new MySQLTableGenerator('table', $structure);
        $generator->parse();
        $generator->cleanUp();

        $schema = $generator->getSchema();
        dd($schema);
    }

    private function cleanUpMigrations($path)
    {
        foreach (glob($path . '/*.php') as $file) {
            unlink($file);
        }
        rmdir($path);
    }

    public function test_writes()
    {
        $generator = new MySQLTableGenerator('table', [
            '`id` int(9) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY',
            '`user_id` int(9) unsigned NOT NULL',
            '`note` varchar(255) NOT NULL',
            'KEY `fk_user_id_idx` (`user_id`)',
            'CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE'
        ]);

        $generator->parse();
        $generator->cleanUp();

        $path = __DIR__ . '/../../migrations';

        if (! is_dir($path)) {
            mkdir($path);
        }
        $generator->write($path);

        $this->assertFileExists($path . '/0000_00_00_000000_create_test_table_table.php');

        $this->cleanUpMigrations($path);
    }
}
