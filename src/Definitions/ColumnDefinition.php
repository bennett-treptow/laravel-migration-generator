<?php

namespace LaravelMigrationGenerator\Definitions;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Helpers\ValueToString;
use LaravelMigrationGenerator\Helpers\WritableTrait;

/**
 * Class ColumnDefinition
 * @package LaravelMigrationGenerator\Definitions
 */
class ColumnDefinition
{
    use WritableTrait;

    protected string $methodName;

    protected array $methodParameters = [];

    protected ?string $columnName;

    protected bool $unsigned = false;

    protected ?bool $nullable = null;

    protected $defaultValue;

    protected ?string $comment = null;

    protected ?string $characterSet = null;

    protected ?string $collation = null;

    protected bool $autoIncrementing = false;

    protected bool $index = false;

    protected bool $primary = false;

    protected bool $unique = false;

    protected bool $useCurrent = false;

    protected bool $useCurrentOnUpdate = false;

    protected ?string $storedAs = null;

    protected ?string $virtualAs = null;

    protected bool $isUUID = false;

    /** @var IndexDefinition[] */
    protected array $indexDefinitions = [];

    public function __construct($attributes = [])
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    //region Getters

    /**
     * @return string
     */
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @return array
     */
    public function getMethodParameters(): array
    {
        return $this->methodParameters;
    }

    /**
     * @return string|null
     */
    public function getColumnName(): ?string
    {
        return $this->columnName;
    }

    /**
     * @return bool
     */
    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    /**
     * @return ?bool
     */
    public function isNullable(): ?bool
    {
        return $this->nullable;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        if (ValueToString::isCastedValue($this->defaultValue)) {
            return ValueToString::parseCastedValue($this->defaultValue);
        }

        return $this->defaultValue;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return string|null
     */
    public function getCharacterSet(): ?string
    {
        return $this->characterSet;
    }

    /**
     * @return string|null
     */
    public function getCollation(): ?string
    {
        return $this->collation;
    }

    /**
     * @return bool
     */
    public function isAutoIncrementing(): bool
    {
        return $this->autoIncrementing;
    }

    /**
     * @return bool
     */
    public function isIndex(): bool
    {
        return $this->index;
    }

    /**
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->primary;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * @return bool
     */
    public function useCurrent(): bool
    {
        return $this->useCurrent;
    }

    /**
     * @return bool
     */
    public function useCurrentOnUpdate(): bool
    {
        return $this->useCurrentOnUpdate;
    }

    public function getStoredAs(): ?string
    {
        return $this->storedAs;
    }

    public function getVirtualAs(): ?string
    {
        return $this->virtualAs;
    }

    /**
     * @return bool
     */
    public function isUUID(): bool
    {
        return $this->isUUID;
    }

    //endregion

    //region Setters

    /**
     * @param string $methodName
     * @return ColumnDefinition
     */
    public function setMethodName(string $methodName): ColumnDefinition
    {
        $this->methodName = $methodName;

        return $this;
    }

    /**
     * @param array $methodParameters
     * @return ColumnDefinition
     */
    public function setMethodParameters(array $methodParameters): ColumnDefinition
    {
        $this->methodParameters = $methodParameters;

        return $this;
    }

    /**
     * @param string|null $columnName
     * @return ColumnDefinition
     */
    public function setColumnName(?string $columnName): ColumnDefinition
    {
        $this->columnName = $columnName;

        return $this;
    }

    /**
     * @param bool $unsigned
     * @return ColumnDefinition
     */
    public function setUnsigned(bool $unsigned): ColumnDefinition
    {
        $this->unsigned = $unsigned;

        return $this;
    }

    /**
     * @param ?bool $nullable
     * @return ColumnDefinition
     */
    public function setNullable(?bool $nullable): ColumnDefinition
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * @param mixed $defaultValue
     * @return ColumnDefinition
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @param string|null $comment
     * @return ColumnDefinition
     */
    public function setComment(?string $comment): ColumnDefinition
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @param string|null $collation
     * @return ColumnDefinition
     */
    public function setCharacterSet(?string $characterSet): ColumnDefinition
    {
        $this->characterSet = $characterSet;

        return $this;
    }

    /**
     * @param string|null $collation
     * @return ColumnDefinition
     */
    public function setCollation(?string $collation): ColumnDefinition
    {
        $this->collation = $collation;

        return $this;
    }

    /**
     * @param bool $autoIncrementing
     * @return ColumnDefinition
     */
    public function setAutoIncrementing(bool $autoIncrementing): ColumnDefinition
    {
        $this->autoIncrementing = $autoIncrementing;

        return $this;
    }

    public function setStoredAs(?string $storedAs): ColumnDefinition
    {
        $this->storedAs = $storedAs;

        return $this;
    }

    public function setVirtualAs(?string $virtualAs): ColumnDefinition
    {
        $this->virtualAs = $virtualAs;

        return $this;
    }

    public function addIndexDefinition(IndexDefinition $definition): ColumnDefinition
    {
        $this->indexDefinitions[] = $definition;

        return $this;
    }

    /**
     * @param bool $index
     * @return ColumnDefinition
     */
    public function setIndex(bool $index): ColumnDefinition
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @param bool $primary
     * @return ColumnDefinition
     */
    public function setPrimary(bool $primary): ColumnDefinition
    {
        $this->primary = $primary;

        return $this;
    }

    /**
     * @param bool $unique
     * @return ColumnDefinition
     */
    public function setUnique(bool $unique): ColumnDefinition
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * @param bool $useCurrent
     * @return ColumnDefinition
     */
    public function setUseCurrent(bool $useCurrent): ColumnDefinition
    {
        $this->useCurrent = $useCurrent;

        return $this;
    }

    /**
     * @param bool $useCurrentOnUpdate
     * @return ColumnDefinition
     */
    public function setUseCurrentOnUpdate(bool $useCurrentOnUpdate): ColumnDefinition
    {
        $this->useCurrentOnUpdate = $useCurrentOnUpdate;

        return $this;
    }

    /**
     * @param bool $isUUID
     * @return ColumnDefinition
     */
    public function setIsUUID(bool $isUUID): ColumnDefinition
    {
        $this->isUUID = $isUUID;

        return $this;
    }

    //endregion

    protected function isNullableMethod($methodName)
    {
        return ! in_array($methodName, ['softDeletes', 'morphs', 'nullableMorphs', 'rememberToken', 'nullableUuidMorphs']) && ! $this->isPrimaryKeyMethod($methodName);
    }

    protected function isPrimaryKeyMethod($methodName)
    {
        return in_array($methodName, ['tinyIncrements', 'mediumIncrements', 'increments', 'bigIncrements', 'id']);
    }

    protected function canBeUnsigned($methodName)
    {
        return ! in_array($methodName, ['morphs', 'nullableMorphs']) && ! $this->isPrimaryKeyMethod($methodName);
    }

    protected function guessLaravelMethod()
    {
        if ($this->primary && $this->unsigned && $this->autoIncrementing) {
            //some sort of increments field
            if ($this->methodName === 'bigInteger') {
                if ($this->columnName === 'id') {
                    return [null, 'id', []];
                } else {
                    return [$this->columnName, 'bigIncrements', []];
                }
            } elseif ($this->methodName === 'mediumInteger') {
                return [$this->columnName, 'mediumIncrements', []];
            } elseif ($this->methodName === 'integer') {
                return [$this->columnName, 'increments', []];
            } elseif ($this->methodName === 'smallInteger') {
                return [$this->columnName, 'smallIncrements', []];
            } elseif ($this->methodName === 'tinyInteger') {
                return [$this->columnName, 'tinyIncrements', []];
            }
        }

        if ($this->methodName === 'tinyInteger' && ! $this->unsigned) {
            $boolean = false;
            if (in_array($this->defaultValue, ['true', 'false', true, false, 'TRUE', 'FALSE', '1', '0', 1, 0], true)) {
                $boolean = true;
            }
            if (Str::startsWith(strtoupper($this->columnName), ['IS_', 'HAS_'])) {
                $boolean = true;
            }
            if ($boolean) {
                return [$this->columnName, 'boolean', []];
            }
        }

        if ($this->methodName === 'morphs' && $this->nullable === true) {
            return [$this->columnName, 'nullableMorphs', []];
        }

        if ($this->methodName === 'uuidMorphs' && $this->nullable === true) {
            return [$this->columnName, 'nullableUuidMorphs', []];
        }

        if ($this->methodName === 'string' && $this->columnName === 'remember_token' && $this->nullable === true) {
            return [null, 'rememberToken', []];
        }
        if ($this->isUUID() && $this->methodName !== 'uuidMorphs') {
            //only override if not already uuidMorphs
            return [$this->columnName, 'uuid', []];
        }

        if (config('laravel-migration-generator.definitions.prefer_unsigned_prefix') && $this->unsigned) {
            $availableUnsignedPrefixes = [
                'bigInteger',
                'decimal',
                'integer',
                'mediumInteger',
                'smallInteger',
                'tinyInteger'
            ];
            if (in_array($this->methodName, $availableUnsignedPrefixes)) {
                return [$this->columnName, 'unsigned' . ucfirst($this->methodName), $this->methodParameters];
            }
        }

        return [$this->columnName, $this->methodName, $this->methodParameters];
    }

    public function render(): string
    {
        [$finalColumnName, $finalMethodName, $finalMethodParameters] = $this->guessLaravelMethod();

        $initialString = '$table->' . $finalMethodName . '(';
        if ($finalColumnName !== null) {
            $initialString .= ValueToString::make($finalColumnName);
        }
        if (count($finalMethodParameters) > 0) {
            foreach ($finalMethodParameters as $param) {
                $initialString .= ', ' . ValueToString::make($param);
            }
        }
        $initialString .= ')';
        if ($this->unsigned && $this->canBeUnsigned($finalMethodName) && ! Str::startsWith($finalMethodName, 'unsigned')) {
            $initialString .= '->unsigned()';
        }

        if ($this->defaultValue === 'NULL') {
            $this->defaultValue = null;
            $this->nullable = true;
        }

        if ($this->isNullableMethod($finalMethodName)) {
            if ($this->nullable === true) {
                $initialString .= '->nullable()';
            }
        }

        if ($this->defaultValue !== null) {
            $initialString .= '->default(';
            $initialString .= ValueToString::make($this->defaultValue, false);
            $initialString .= ')';
        }
        if ($this->useCurrent) {
            $initialString .= '->useCurrent()';
        }
        if ($this->useCurrentOnUpdate) {
            $initialString .= '->useCurrentOnUpdate()';
        }

        if ($this->index) {
            $indexName = '';
            if (count($this->indexDefinitions) === 1 && config('laravel-migration-generator.definitions.use_defined_index_names')) {
                $indexName = ValueToString::make($this->indexDefinitions[0]->getIndexName());
            }
            $initialString .= '->index(' . $indexName . ')';
        }

        if ($this->primary && ! $this->isPrimaryKeyMethod($finalMethodName)) {
            $indexName = '';
            if (count($this->indexDefinitions) === 1 && config('laravel-migration-generator.definitions.use_defined_primary_key_index_names')) {
                if ($this->indexDefinitions[0]->getIndexName() !== null) {
                    $indexName = ValueToString::make($this->indexDefinitions[0]->getIndexName());
                }
            }
            $initialString .= '->primary(' . $indexName . ')';
        }

        if ($this->unique) {
            $indexName = '';
            if (count($this->indexDefinitions) === 1 && config('laravel-migration-generator.definitions.use_defined_unique_key_index_names')) {
                $indexName = ValueToString::make($this->indexDefinitions[0]->getIndexName());
            }
            $initialString .= '->unique(' . $indexName . ')';
        }

        if ($this->storedAs !== null) {
            $initialString .= '->storedAs(' . ValueToString::make(str_replace('"', '\"', $this->storedAs), false, false) . ')';
        }

        if ($this->virtualAs !== null) {
            $initialString .= '->virtualAs(' . ValueToString::make(str_replace('"', '\"', $this->virtualAs), false, false) . ')';
        }

        if ($this->comment !== null && config('laravel-migration-generator.definitions.with_comments')) {
            $initialString .= '->comment(' . ValueToString::make(str_replace('"', '\"', $this->comment), false, false) . ')';
        }

        return $initialString;
    }
}
