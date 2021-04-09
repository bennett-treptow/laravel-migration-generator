<?php
namespace LaravelMigrationGenerator\Generators;

interface TableGeneratorInterface {
    public function parse();
    public function finalPass();
    public function write(string $basePath);

    public function getIndices();
}