<?php


namespace PhpFlags;


class ArgSpec implements MixAppendOption, TypingValue
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $description;
    /**
     * @var mixed|null
     */
    private $defaultValue;
    /**
     * @var array|null
     */
    private $validValues;
    /**
     * @var bool
     */
    private $required;
    /**
     * @var bool
     */
    private $multiple;
    /**
     * @var Type
     */
    private $type;
    /**
     * @var Value
     */
    private $value;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->description = null;
        $this->defaultValue = null;
        $this->validValues = null;
        $this->required = false;
        $this->multiple = false;
        $this->type = null;
        $this->value = new Value();
    }

    public function desc(string $description): MixAppendOption
    {
        $this->description = $description;

        return $this;
    }

    public function default($default): MixAppendOption
    {
        // TODO: check required

        $this->defaultValue = $default;

        return $this;
    }

    public function valid(array $values): MixAppendOption
    {
        $this->validValues = $values;

        return $this;
    }

    public function int(): Value
    {
        $this->type = Type::INT();

        return $this->value;
    }

    public function float(): Value
    {
        $this->type = Type::FLOAT();

        return $this->value;
    }

    public function bool(): Value
    {
        $this->type = Type::BOOL();

        return $this->value;
    }

    public function string(): Value
    {
        $this->type = Type::STRING();

        return $this->value;
    }

    public function date(): Value
    {
        $this->type = Type::DATE();

        return $this->value;
    }

    public function allowMultiple(): bool
    {
        return $this->multiple;
    }

    public function getDefault()
    {
        return $this->defaultValue;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setValue($value)
    {
        $typedValue = $this->type->getTypedValue($value);
        $this->value->set($typedValue);
    }
}