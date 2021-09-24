<?php

namespace LaravelMigrationGenerator\Definitions;

use LaravelMigrationGenerator\Formatters\ViewFormatter;

class ViewDefinition
{
    protected string $driver;

    protected string $viewName;

    protected ?string $schema;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function setDriver(string $driver): ViewDefinition
    {
        $this->driver = $driver;

        return $this;
    }

    public function getSchema(): string
    {
        return $this->schema;
    }

    public function setSchema(string $schema): ViewDefinition
    {
        $this->schema = $schema;

        return $this;
    }

    public function getViewName(): string
    {
        return $this->viewName;
    }

    public function setViewName(string $viewName): ViewDefinition
    {
        $this->viewName = $viewName;

        return $this;
    }

    public function formatter(): ViewFormatter
    {
        return new ViewFormatter($this);
    }
}
