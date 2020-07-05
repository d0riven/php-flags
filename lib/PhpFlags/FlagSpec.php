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
        $this->value = new Value();
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

    // 以下のgetter配下はいい感じに出来そうな気がする
    public function getLong(): string
    {
        return '--' . $this->flagName;
    }

    public function getShort(): string
    {
        return '-' . $this->short;
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

    public function getRequired(): bool
    {
        return $this->required;
    }

    public function getType(): Type
    {
        return $this->type;
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