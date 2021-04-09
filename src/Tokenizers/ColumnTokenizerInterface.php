<?php

namespace LaravelMigrationGenerator\Tokenizers;

use LaravelMigrationGenerator\Generators\TableGeneratorInterface;

interface ColumnTokenizerInterface
{
    public function getColumnName(): string;

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
