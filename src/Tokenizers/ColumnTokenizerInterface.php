<?php

namespace LaravelMigrationGenerator\Tokenizers;

use LaravelMigrationGenerator\Generators\TableGeneratorInterface;

interface ColumnTokenizerInterface
{
    /**
     * Get the column name for this instance
     * Should only be null if the corresponding Blueprint method
     * does not require an argument, such as id(), timestamps()
     */
    public function getColumnName(): ?string;

    public function getColumnType(): string;

    public function getMethod(): string;

    public function getNullable(): bool;

    public function getDefaultValue();

    public function getMethodParameters(): array;

    public function getCollation(): ?string;

    public function getUnsigned(): bool;

    public function getPrimaryKey(): bool;

    public function getUseCurrent(): bool;

    public function toMethod(): string;

    public function tokenize(): self;

    public function finalPass(TableGeneratorInterface $tableGenerator): ?bool;
}
