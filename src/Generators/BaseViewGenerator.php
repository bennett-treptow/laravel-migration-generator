<?php

namespace LaravelMigrationGenerator\Generators;

use LaravelMigrationGenerator\Generators\Concerns\WritesViewsToFile;
use LaravelMigrationGenerator\Helpers\WritableTrait;
use LaravelMigrationGenerator\Generators\Interfaces\ViewGeneratorInterface;

abstract class BaseViewGenerator implements ViewGeneratorInterface
{
    use WritableTrait;
    use WritesViewsToFile;

    protected string $viewName;

    protected ?string $schema;

    public function __construct(string $viewName, ?string $schema = null)
    {
        $this->viewName = $viewName;
        $this->schema = $schema;
    }

    public static function init(string $viewName)
    {
        $obj = new static($viewName);
        $obj->parse();

        return $obj;
    }

    public function getSchema(): ?string
    {
        return $this->schema;
    }

    public function shouldResolveStructure(): bool
    {
        return $this->schema === null;
    }
}
