<?php
declare(strict_types=1);


namespace PhpFlags;


class FlagSpec implements MixAppendOption, FlagAppendOption, TypingValue
{
    /**
     * @var string
     */
    private $flagName;
    /**
     * @var string|null
     */
    private $description;
    /**
     * @var string|null
     */
    private $short;
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
     * @var Type|null
     */
    private $type;
    /**
     * @var Value
     */
    private $value;

    public function __construct(string $flagName)
    {
        $this->flagName = $flagName;
        $this->description = null;
        $this->short = null;
        $this->defaultValue = null;
        $this->validValues = null;
        $this->required = false;
        $this->multiple = false;
        $this->type = null;
        $this->value = null;
    }

    public function desc(string $describe): MixAppendOption
    {
        $this->description = $describe;

        return $this;
    }

    public function short(string $short): MixAppendOption
    {
        $this->short = $short;

        return $this;
    }

    public function default($value): MixAppendOption
    {
        // TODO: check required

        $this->defaultValue = $value;

        return $this;
    }

    public function valid(array $values): MixAppendOption
    {
        $this->validValues = $values;

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

    // 以下のgetter配下はいい感じに出来そうな気がする
    public function getLong(): string
    {
        return '--' . $this->flagName;
    }

    public function getShort(): string
    {
        return '-' . $this->short;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return mixed|null
     */
    public function getDefault()
    {
        return $this->defaultValue;
    }

    public function hasDefault(): bool
    {
        return $this->defaultValue !== null;
    }

    public function hasShort(): bool
    {
        return $this->short !== null;
    }

    public function hasDescription(): bool
    {
        return $this->description !== null;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getRequired(): bool
    {
        return $this->required;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getValue(): Value
    {
        return $this->value;
    }

    public function setValue($value)
    {
        // boolは呼び出し側でbooleanしか渡さないという想定
        if ($this->getType()->equals(TYPE::BOOL())) {
            $this->value->set($value);

            return;
        }
        $typedValue = $this->type->getTypedValue($value);
        $this->value->set($typedValue);
    }
}