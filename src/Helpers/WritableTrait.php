<?php

namespace LaravelMigrationGenerator\Helpers;

trait WritableTrait
{
    public bool $writable = true;

    public function markAsWritable(bool $writable = true)
    {
        $this->writable = $writable;

        return $this;
    }

    public function isWritable()
    {
        return $this->writable;
    }
}
