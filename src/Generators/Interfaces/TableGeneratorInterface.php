<?php

namespace LaravelMigrationGenerator\Generators\Interfaces;

interface TableGeneratorInterface
{
    public function shouldResolveStructure(): bool;

    public function resolveStructure();

    public function getSchema($tab = ''): string;

    public function parse();

    public function cleanUp();

    public function write(string $basePath);

    public function getIndices();
}
