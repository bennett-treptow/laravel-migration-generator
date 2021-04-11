<?php

namespace LaravelMigrationGenerator\Generators\Interfaces;

interface ViewGeneratorInterface
{
    public static function driver(): string;

    public function parse();

    public function resolveSchema();

    public function write(string $basePath, string $tabCharacter = '    ');

    public function getSchema(): ?string;
}
