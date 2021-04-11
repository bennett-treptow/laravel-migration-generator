<?php

namespace LaravelMigrationGenerator\GeneratorManagers\Interfaces;

use Illuminate\Console\OutputStyle;

interface GeneratorManagerInterface
{
    public function handle(string $basePath, array $tableNames = [], OutputStyle $output);
}
