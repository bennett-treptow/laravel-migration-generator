<?php

namespace LaravelMigrationGenerator\Helpers;

class Formatter
{
    private $lines = [];

    private string $tabCharacter;

    private bool $isSpace = true;

    public function __construct(string $tabCharacter = '    ')
    {
        $this->tabCharacter = $tabCharacter;
        $this->isSpace = strpos($tabCharacter, "\t") === false;
    }

    public function line(string $data, $indentTimes = 0)
    {
        $this->lines[] = str_repeat($this->tabCharacter, $indentTimes) . $data;

        return function ($data) use ($indentTimes) {
            return $this->line($data, $indentTimes + 1);
        };
    }

    public function render($extraIndent = 0)
    {
        $lines = $this->lines;
        if ($extraIndent > 0) {
            $lines = collect($lines)->map(function ($item, $index) use ($extraIndent) {
                if ($index === 0) {
                    return $item;
                }

                return str_repeat($this->tabCharacter, $extraIndent) . $item;
            })->toArray();
        }

        return implode("\n", $lines);
    }

    public function replaceOnLine($toReplace, $body)
    {
        if (preg_match('/^(\s+)?' . preg_quote($toReplace) . '/m', $body, $matches) !== false) {
            $gap = $matches[1] ?? '';
            $numSpaces = strlen($this->tabCharacter);
            if ($numSpaces === 0) {
                $startingTabIndent = 0;
            } else {
                $startingTabIndent = (int) (strlen($gap) / $numSpaces);
            }

            return preg_replace('/' . preg_quote($toReplace) . '/', $this->render($startingTabIndent), $body);
        }

        return $body;
    }

    public static function replace($tabCharacter, $toReplace, $replacement, $body)
    {
        $formatter = new static($tabCharacter);
        foreach (explode("\n", $replacement) as $line) {
            $formatter->line($line);
        }

        return $formatter->replaceOnLine($toReplace, $body);
    }
}
