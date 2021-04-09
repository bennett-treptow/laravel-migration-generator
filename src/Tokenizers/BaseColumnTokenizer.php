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

    protected $useCurrent = false;

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
        $value = $this->defaultValue;

        if($value !== null && Str::contains($value, 'float$:')){
            $value = str_replace('float$:', '', $value);
        }

        return $value;
    }
    public function getUnsigned(): bool
    {
        return $this->unsigned;
    }

    public function getUseCurrent(): bool{
        return $this->useCurrent;
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
        $finalParameters = $this->methodParameters;
        if ($this->unsigned) {
            $finalMethod = 'unsigned' . Str::ucfirst($this->method);
        }

        if ($finalMethod === 'unsignedBigInteger' && $this->primaryKey) {
            $finalMethod = 'id';
            $finalParameters = [];
        }
        if ($finalMethod === 'unsignedInteger' && $this->primaryKey) {
            $finalMethod = 'increments';
            $finalParameters = [];
        }

        if ($finalMethod === 'tinyInteger' && ($this->defaultValue == '1' || $this->defaultValue == '0')) {
            $finalMethod = 'boolean';
            $finalParameters = [];
        }

        $initialString = '$table->' . $finalMethod . '(';
        if ($this->columnName !== null) {
            $initialString .= $this->valueToString($this->columnName);
        }
        if (count($finalParameters) > 0) {
            foreach ($finalParameters as $param) {
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
            $initialString .= $this->valueToString($this->defaultValue, false);
            $initialString .= ')';
        }
        if($this->useCurrent){
            $initialString .= '->useCurrent()';
        }

        if ($this->primaryKey && ! in_array($finalMethod, ['increments', 'bigIncrements', 'id'])) {
            $initialString .= '->primary()';
        }

        return $initialString;
    }
}