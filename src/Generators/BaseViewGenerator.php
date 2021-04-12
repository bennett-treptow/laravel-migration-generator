<?php

namespace LaravelMigrationGenerator\Generators;

use LaravelMigrationGenerator\Helpers\WritableTrait;
use LaravelMigrationGenerator\Generators\Concerns\WritesViewsToFile;
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

    public static function init(string $viewName, ?string $schema = null)
    {
        $obj = new static($viewName, $schema);
        if ($obj->shouldResolveStructure()) {
            $obj->resolveSchema();
        }
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
