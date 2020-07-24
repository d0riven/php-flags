<?php


namespace PhpFlags;


trait TypingValueTrait
{
    /**
     * @var SingleValue
     */
    private $value;
    /**
     * @var bool
     */
    private $multiple;

    public function int(string $valueName = 'int'): Value
    {
        $type = Type::INT();
        $this->value = $this->allowMultiple() ?
            new MultipleValue($type, $valueName) :
            new SingleValue($type, $valueName);

        return $this->value;
    }

    public function float(string $valueName = 'float'): Value
    {
        $type = Type::FLOAT();
        $this->value = $this->allowMultiple() ?
            new MultipleValue($type, $valueName) :
            new SingleValue($type, $valueName);

        return $this->value;
    }

    public function bool(): Value
    {
        $type = Type::BOOL();
        $this->value = $this->allowMultiple() ?
            new MultipleValue($type, null) :
            new SingleValue($type, null);

        return $this->value;
    }

    public function string(string $valueName = 'string'): Value
    {
        $type = Type::STRING();
        $this->value = $this->allowMultiple() ?
            new MultipleValue($type, $valueName) :
            new SingleValue($type, $valueName);

        return $this->value;
    }

    public function date(string $valueName = 'date'): Value
    {
        $type = Type::DATE();
        $this->value = $this->allowMultiple() ?
            new MultipleValue($type, $valueName) :
            new SingleValue($type, $valueName);

        return $this->value;
    }

    public function getType(): Type
    {
        return $this->value->type();
    }

    public function getValue(): Value
    {
        return $this->value;
    }

    public function getName(): string
    {
        return $this->getValue()->name();
    }

    public function multiple()
    {
        $this->multiple = true;

        return $this;
    }

    public function allowMultiple(): bool
    {
        return $this->multiple;
    }
}