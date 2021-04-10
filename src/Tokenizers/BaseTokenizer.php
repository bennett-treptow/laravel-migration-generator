<?php

namespace LaravelMigrationGenerator\Tokenizers;

abstract class BaseTokenizer
{
    protected $tokens = [];

    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
        $this->tokens = array_map(fn ($item) => trim($item, ', '), str_getcsv($value, ' ', "'"));
    }

    public static function make(string $line)
    {
        return new static($line);
    }

    /**
     * @param string $line
     * @return static
     */
    public static function parse(string $line)
    {
        return (new static($line))->tokenize();
    }

    protected function parseColumn($value)
    {
        return trim($value, '` ');
    }

    protected function columnsToArray($string)
    {
        $string = trim($string, '()');

        return array_map(fn ($item) => $this->parseColumn($item), explode(',', $string));
    }

    protected function consume()
    {
        return array_shift($this->tokens);
    }

    protected function putBack($value)
    {
        array_unshift($this->tokens, $value);
    }
}
