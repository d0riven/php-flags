<?php


namespace PhpFlags;


use Closure;

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
     * @var Closure|null
     */
    private $validRule;
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

    public function __construct()
    {
        $this->description = null;
        $this->defaultValue = null;
        $this->validRule = null;
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

    public function validRule(Closure $validRule): MixAppendOption
    {
        $this->validRule = $validRule;

        return $this;
    }

    public function required(): MixAppendOption
    {
        $this->required = true;

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
        return $this->getValue()->name();
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

    public function getValidRule():?Closure
    {
        return $this->validRule;
    }

    public function setValue($value)
    {
        // TODO: Compositeを使っていい感じにする
        if ($this->allowMultiple()) {
            if (!is_array($value)) {
                throw new InvalidArgumentsException(sprintf('is not array. value:[%s]', implode(',', $value)));
            }
            $typedValues = [];
            foreach ($value as $v) {
                $typedValues[] = $this->type->getTypedValue($v);
            }
            $this->value->set($typedValues);
            return;
        }
        $typedValue = $this->type->getTypedValue($value);
        $this->value->set($typedValue);
    }
}