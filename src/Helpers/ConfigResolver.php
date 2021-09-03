<?php

namespace LaravelMigrationGenerator\Helpers;

class ConfigResolver
{
    protected static function resolver(string $configKey, string $driver)
    {
        return ($override = config('laravel-migration-generator.' . $driver . '.' . $configKey)) !== null ?
            $override : config('laravel-migration-generator.' . $configKey);
    }

    public static function tableNamingScheme(string $driver)
    {
        return static::resolver('table_naming_scheme', $driver);
    }

    public static function viewNamingScheme(string $driver)
    {
        return static::resolver('view_naming_scheme', $driver);
    }

    public static function path(string $driver)
    {
        return static::resolver('path', $driver);
    }

    public static function skippableTables(string $driver)
    {
        return array_map('trim', explode(',', static::resolver('skippable_tables', $driver)));
    }

    public static function skippableViews(string $driver)
    {
        return array_map('trim', explode(',', static::resolver('skippable_views', $driver)));
    }
}
