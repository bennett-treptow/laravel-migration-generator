<?php

namespace LaravelMigrationGenerator\Tokenizers;

interface IndexTokenizerInterface
{
    public function getIndexType(): string;

    public function getIndexName(): string;

    public function getIndexColumns();

    public function getForeignReferencedColumns();

    public function getForeignReferencedTable(): ?string;

    public function getConstraintActions(): array;

    public function tokenize(): self;

    public function toMethod(): string;
}
