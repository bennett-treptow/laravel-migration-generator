<?php

namespace LaravelMigrationGenerator\Helpers;

use Carbon\Carbon;

class Time
{
    private Carbon $current;

    public function __construct()
    {
        $this->current = Carbon::now()->startOfHour();
    }

    public function format(string $format): string
    {
        $result = $this->current->format($format);
        $this->current = $this->current->addSecond();

        return $result;
    }
}
