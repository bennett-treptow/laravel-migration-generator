<?php

namespace LaravelMigrationGenerator\GeneratorManagers\Interfaces;

interface GeneratorManagerInterface
{
    public function handle(string $basePath, ?string $singleTable = null);
}
