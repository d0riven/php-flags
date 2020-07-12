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
        $this->value = null;
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

    public function multiple(): MixAppendOption
    {
        $this->multiple = true;

        return $this;
    }

    public function int(string $valueName): Value
    {
        $this->type = Type::INT();
        $this->value = new Value($valueName);

        return $this->value;
    }

    public function float(string $valueName): Value
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

    public function string(string $valueName): Value
    {
        $this->type = Type::STRING();
        $this->value = new Value($valueName);

        return $this->value;
    }

    public function date(string $valueName): Value
    {
        $this->type = Type::DATE();
        $this->value = new Value($valueName);

        return $this->value;
    }

    public function allowMultiple(): bool
    {
        return $this->multiple;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDefault()
    {
        return $this->defaultValue;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function hasDefault(): bool
    {
        return $this->defaultValue !== null;
    }

    public function hasDescription(): bool
    {
        return $this->description !== null;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $typedValue = $this->type->getTypedValue($value);
        $this->value->set($typedValue);
    }
}