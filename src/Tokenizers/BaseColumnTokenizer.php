<?php
namespace LaravelMigrationGenerator\Tokenizers;

use Illuminate\Support\Str;

abstract class BaseColumnTokenizer extends BaseTokenizer implements ColumnTokenizerInterface {
    protected $columnName;

    protected $columnType;

    protected $collation;

    protected $method;

    protected $methodParameters = [];

    protected $nullable = true;

    protected $unsigned = false;

    protected $defaultValue;

    protected $primaryKey = false;

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function getColumnType(): string
    {
        return $this->columnType;
    }

    public function getMethod(): string {
        return $this->method;
    }
    public function getNullable(): bool {
        return $this->nullable;
    }
    public function getDefaultValue(){
        return $this->defaultValue;
    }
    public function getUnsigned(): bool
    {
        return $this->unsigned;
    }

    public function getMethodParameters(): array {
        return $this->methodParameters;
    }
    public function getCollation(): ?string {
        return $this->collation;
    }

    public function getPrimaryKey(): bool
    {
        return $this->primaryKey;
    }

    public function toMethod(): string
    {
        $finalMethod = $this->method;
        if ($this->unsigned) {
            $finalMethod = 'unsigned' . Str::ucfirst($this->method);
        }

        if ($finalMethod === 'unsignedBigInteger' && $this->primaryKey) {
            $finalMethod = 'id';
        }
        if ($finalMethod === 'unsignedInteger' && $this->primaryKey) {
            $finalMethod = 'increments';
        }

        if ($finalMethod === 'tinyInteger' && ($this->defaultValue == '1' || $this->defaultValue == '0')) {
            $finalMethod = 'boolean';
        }

        $initialString = '$table->' . $finalMethod . '(';
        if ($this->columnName !== null) {
            $initialString .= $this->valueToString($this->columnName);
        }
        if (count($this->methodParameters) > 0) {
            foreach ($this->methodParameters as $param) {
                $initialString .= ', ' . $this->valueToString($param);
            }
        }
        $initialString .= ')';
        if ($this->nullable) {
            $initialString .= '->nullable()';
        }
        if ($this->defaultValue === 'NULL') {
            $this->defaultValue = null;
            $this->nullable = true;
        }
        if (($this->nullable && $this->defaultValue !== null) || $this->defaultValue !== null) {
            $initialString .= '->default(';
            $initialString .= $this->valueToString($this->defaultValue);
            $initialString .= ')';
        }

        if ($this->primaryKey && ! in_array($finalMethod, ['increments', 'bigIncrements', 'id'])) {
            $initialString .= '->primary()';
        }

        return $initialString;
    }
}