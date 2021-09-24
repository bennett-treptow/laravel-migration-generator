<?php

namespace Tests\Unit\Generators;

use Tests\TestCase;
use LaravelMigrationGenerator\Generators\MySQL\ViewGenerator;

class MySQLViewGeneratorTest extends TestCase
{
    public function tearDown(): void
    {
        parent::tearDown();

        $path = __DIR__ . '/../../migrations';
        $this->cleanUpMigrations($path);
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

    public function test_generates()
    {
        $generator = ViewGenerator::init('viewName', 'CREATE ALGORITHM=UNDEFINED DEFINER=`homestead`@`%` SQL SECURITY DEFINER VIEW `view_client_config` AS select `cfg`.`client_id` AS `client_id`,(case when (`cfg`.`client_type_can_edit` = 1) then 1 when (isnull(`cfg`.`client_type_can_edit`) and (`cfg`.`default_can_edit` = 1)) then 1 else 0 end) AS `can_edit` from `table` `cfg`');

        $this->assertStringStartsWith('CREATE VIEW `view_client_config` AS', $generator->definition()->getSchema());
    }

    public function test_writes()
    {
        $generator = ViewGenerator::init('viewName', 'CREATE ALGORITHM=UNDEFINED DEFINER=`homestead`@`%` SQL SECURITY DEFINER VIEW `view_client_config` AS select `cfg`.`client_id` AS `client_id`,(case when (`cfg`.`client_type_can_edit` = 1) then 1 when (isnull(`cfg`.`client_type_can_edit`) and (`cfg`.`default_can_edit` = 1)) then 1 else 0 end) AS `can_edit` from `table` `cfg`');
        $path = __DIR__ . '/../../migrations';

        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $written = $generator->definition()->formatter()->write($path);
        $this->assertFileExists($written);
    }
}
