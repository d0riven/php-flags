<?php


namespace PhpFlags;


trait TypingValueTrait
{
    /**
     * @var Type|null
     */
    private $type;
    /**
     * @var Value
     */
    private $value;

    public function int(string $valueName = 'int'): Value
    {
        $this->type = Type::INT();
        $this->value = new Value($valueName);

        return $this->value;
    }

    public function float(string $valueName = 'float'): Value
    {
        $this->type = Type::FLOAT();
        $this->value = new Value($valueName);

        return $this->value;
    }

    public function bool(): Value
    {
        $this->type = Type::BOOL();
        $this->value = new Value(null);

        return $this->value;
    }

    public function string(string $valueName = 'string'): Value
    {
        $this->type = Type::STRING();
        $this->value = new Value($valueName);

        return $this->value;
    }

    public function date(string $valueName = 'date'): Value
    {
        $this->type = Type::DATE();
        $this->value = new Value($valueName);

        return $this->value;
    }

    public function getType(): Type
    {
        return $this->type;
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