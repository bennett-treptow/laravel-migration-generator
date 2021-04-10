<?php

namespace LaravelMigrationGenerator\Generators\Interfaces;

interface ViewGeneratorInterface
{
    public function parse();

    public function write(string $basePath);
}
