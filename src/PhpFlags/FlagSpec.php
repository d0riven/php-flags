<?php
declare(strict_types=1);


namespace PhpFlags;


class FlagSpec
{
    use FlagArgAppendOptionTrait;
    use TypingValueTrait;

    /**
     * @var string
     */
    private $flagName;
    /**
     * @var string|null
     */
    private $short;
    /**
     * @var bool
     */
    private $required;

    public function __construct(string $flagName)
    {
        $this->flagName = $flagName;
        $this->description = null;
        $this->short = null;
        $this->defaultValue = null;
        $this->validRule = null;
        $this->required = false;
        $this->multiple = false;
        $this->type = null;
        $this->value = null;
    }

    public function short(string $short)
    {
        $this->short = $short;

        return $this;
    }

    // 以下のgetter配下はいい感じに出来そうな気がする
    public function getLong(): string
    {
        return '--' . $this->flagName;
    }

    public function getShort(): ?string
    {
        if ($this->short === null) {
            return null;
        }

        return '-' . $this->short;
    }

    public function hasShort(): bool
    {
        return $this->short !== null;
    }

    public function required()
    {
        $this->required = true;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getRequired(): bool
    {
        return $this->required;
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
        // boolは呼び出し側でbooleanしか渡さないという想定
        if ($this->getType()->equals(TYPE::BOOL())) {
            $this->value->set($value);

            return;
        }
        $typedValue = $this->type->getTypedValue($value);
        $this->value->set($typedValue);
    }
}