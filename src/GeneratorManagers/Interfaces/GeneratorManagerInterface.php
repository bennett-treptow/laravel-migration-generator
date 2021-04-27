<?php

namespace LaravelMigrationGenerator\GeneratorManagers\Interfaces;

use Illuminate\Console\OutputStyle;

interface GeneratorManagerInterface
{
    public function handle(string $basePath, OutputStyle $output, array $tableNames = []);
}
