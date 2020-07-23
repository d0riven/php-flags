<?php


namespace PhpFlags;


trait TypingValueTrait
{
    /**
     * @var Value
     */
    private $value;

    public function int(string $valueName = 'int'): Value
    {
        $this->value = new Value(Type::INT(), $valueName);

        return $this->value;
    }

    public function float(string $valueName = 'float'): Value
    {
        $this->value = new Value(Type::FLOAT(), $valueName);

        return $this->value;
    }

    public function bool(): Value
    {
        $this->value = new Value(Type::BOOL(), null);

        return $this->value;
    }

    public function string(string $valueName = 'string'): Value
    {
        $this->value = new Value(Type::STRING(), $valueName);

        return $this->value;
    }

    public function date(string $valueName = 'date'): Value
    {
        $this->value = new Value(Type::DATE(), $valueName);

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

}