<?php

namespace LaravelMigrationGenerator\Generators\Interfaces;

interface TableGeneratorInterface
{
    public static function driver(): string;

    public function shouldResolveStructure(): bool;

    public function resolveStructure();

    public function getSchema($tab = ''): string;

    public function parse();

    public function cleanUp();

    public function write(string $basePath, string $tabCharacter = '    ');

    public function getIndices();
}
