<?php

namespace LaravelMigrationGenerator\Definitions;

use Illuminate\Support\Str;
use LaravelMigrationGenerator\Helpers\ValueToString;

/**
 * Class ColumnDefinition
 * @package LaravelMigrationGenerator\Definitions
 */
class ColumnDefinition
{
    protected string $methodName;

    protected array $methodParameters = [];

    protected ?string $columnName;

    protected bool $unsigned = false;

    protected bool $nullable = true;

    protected $defaultValue;

    protected ?string $collation = null;

    protected bool $index = false;

    protected bool $primary = false;

    protected bool $unique = false;

    protected bool $useCurrent = false;

    protected bool $isUUID = false;

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
     * @return bool
     */
    public function isNullable(): bool
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
    public function getCollation(): ?string
    {
        return $this->collation;
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
     * @param bool $nullable
     * @return ColumnDefinition
     */
    public function setNullable(bool $nullable): ColumnDefinition
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
     * @param string|null $collation
     * @return ColumnDefinition
     */
    public function setCollation(?string $collation): ColumnDefinition
    {
        $this->collation = $collation;

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
        return ! in_array($methodName, ['softDeletes', 'morphs', 'nullableMorphs', 'rememberToken']);
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
        if ($this->primary && $this->unsigned) {
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

        if ($this->methodName === 'morphs' && $this->nullable) {
            return [$this->columnName, 'nullableMorphs', $this->methodParameters];
        }

        if ($this->methodName === 'string' && $this->columnName === 'remember_token' && $this->nullable) {
            return [null, 'rememberToken', []];
        }
        if ($this->isUUID() && $this->methodName !== 'uuidMorphs') {
            //only override if not already uuidMorphs
            return [$this->columnName, 'uuid', []];
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
        if ($this->unsigned && $this->canBeUnsigned($finalMethodName)) {
            $initialString .= '->unsigned()';
        }
        if ($this->nullable && $this->isNullableMethod($finalMethodName)) {
            $initialString .= '->nullable()';
        }
        if ($this->defaultValue === 'NULL') {
            $this->defaultValue = null;
            $this->nullable = true;
        }
        if (($this->nullable && $this->defaultValue !== null) || $this->defaultValue !== null) {
            $initialString .= '->default(';
            $initialString .= ValueToString::make($this->defaultValue, false);
            $initialString .= ')';
        }
        if ($this->useCurrent) {
            $initialString .= '->useCurrent()';
        }

        if ($this->index) {
            $initialString .= '->index()';
        }

        if ($this->primary && ! $this->isPrimaryKeyMethod($finalMethodName)) {
            $initialString .= '->primary()';
        }

        if ($this->unique) {
            $initialString .= '->unique()';
        }

        return $initialString;
    }
}
