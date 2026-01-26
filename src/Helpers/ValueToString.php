<?php

namespace LaravelMigrationGenerator\Helpers;

use Illuminate\Support\Str;

class ValueToString
{
    public static function castFloat($value)
    {
        return 'float$:'.$value;
    }

    public static function castBinary($value)
    {
        return 'binary$:'.$value;
    }

    public static function isCastedValue($value)
    {
        return Str::startsWith($value, ['float$:', 'binary$:']);
    }

    public static function parseCastedValue($value)
    {
        if (Str::startsWith($value, 'float$:')) {
            return str_replace('float$:', '', $value);
        }
        if (Str::startsWith($value, 'binary$:')) {
            return 'b\''.str_replace('binary$:', '', $value).'\'';
        }

        return $value;
    }

    /**
     * Escape a string value for safe inclusion in generated PHP code.
     * Prevents PHP injection through crafted index/column names.
     *
     * @param  string  $value  The value to escape
     * @param  bool  $singleQuote  Whether the string will be wrapped in single quotes (true) or double quotes (false)
     */
    public static function escape(string $value, bool $singleQuote = true): string
    {
        // Escape backslashes first
        $escaped = str_replace('\\', '\\\\', $value);

        // Then escape the quote character being used as delimiter
        if ($singleQuote) {
            return str_replace('\'', '\\\'', $escaped);
        } else {
            return str_replace('"', '\\"', $escaped);
        }
    }

    public static function make($value, $singleOutArray = false, $singleQuote = true)
    {
        $quote = $singleQuote ? '\'' : '"';
        if ($value === null) {
            return 'null';
        } elseif (is_array($value)) {
            if ($singleOutArray && count($value) === 1) {
                return $quote.static::escape($value[0], $singleQuote).$quote;
            }

            return '['.collect($value)->map(fn ($item) => $quote.static::escape($item, $singleQuote).$quote)->implode(', ').']';
        } elseif (is_int($value) || is_float($value)) {
            return $value;
        }

        if (static::isCastedValue($value)) {
            return static::parseCastedValue($value);
        }

        if (Str::startsWith($value, $quote) && Str::endsWith($value, $quote)) {
            return $value;
        }

        return $quote.static::escape($value, $singleQuote).$quote;
    }
}
