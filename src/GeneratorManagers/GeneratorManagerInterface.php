<?php

namespace LaravelMigrationGenerator\GeneratorManagers;

interface GeneratorManagerInterface
{
    public function handle(string $basePath, ?string $singleTable = null);
}
