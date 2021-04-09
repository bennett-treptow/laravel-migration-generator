<?php
namespace Tests;

use LaravelMigrationGenerator\LaravelMigrationGeneratorProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelMigrationGeneratorProvider::class
        ];
    }
}