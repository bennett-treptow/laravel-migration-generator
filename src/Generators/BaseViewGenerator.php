<?php

namespace LaravelMigrationGenerator\Generators;

use LaravelMigrationGenerator\Definitions\ViewDefinition;
use LaravelMigrationGenerator\Generators\Interfaces\ViewGeneratorInterface;

abstract class BaseViewGenerator implements ViewGeneratorInterface
{
    protected ViewDefinition $definition;

    public function __construct(string $viewName, ?string $schema = null)
    {
        $this->definition = new ViewDefinition([
            'driver'   => static::driver(),
            'viewName' => $viewName,
            'schema'   => $schema
        ]);
    }

    public function definition(): ViewDefinition
    {
        return $this->definition;
    }

    public static function init(string $viewName, ?string $schema = null)
    {
        $obj = new static($viewName, $schema);
        if ($schema === null) {
            $obj->resolveSchema();
        }
        $obj->parse();

        return $obj;
    }
}
