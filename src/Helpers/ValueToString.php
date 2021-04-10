<?php

namespace LaravelMigrationGenerator\Helpers;

use Illuminate\Support\Str;

class ValueToString
{
    public static function castFloat($value)
    {
        return 'float$:' . $value;
    }

    public static function isCastedValue($value)
    {
        return Str::startsWith($value, ['float$:']);
    }

    public static function parseCastedValue($value)
    {
        if (Str::startsWith($value, 'float$:')) {
            return str_replace('float$:', '', $value);
        }

        return $value;
    }

    public static function make($value, $singleOutArray = false)
    {
        if ($value === null) {
            return 'null';
        } elseif (is_array($value)) {
            if ($singleOutArray && count($value) === 1) {
                return '\'' . $value[0] . '\'';
            }

            return '[' . collect($value)->map(fn ($item) => "'$item'")->implode(', ') . ']';
        } elseif (is_integer($value) || is_float($value)) {
            return $value;
        }

        if (static::isCastedValue($value)) {
            return static::parseCastedValue($value);
        }

        if (Str::startsWith($value, '\'') && Str::endsWith($value, '\'')) {
            return $value;
        }

        return '\'' . $value . '\'';
    }
}
