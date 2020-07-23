<?php


namespace PhpFlags;


use Closure;

class ArgSpec
{
    use FlagArgAppendOptionTrait;
    use TypingValueTrait;

    /**
     * @var string
     */
    private $name;
    /**
     * @var bool
     */
    private $required;

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

    public function required()
    {
        $this->required = true;

        return $this;
    }

    public function isRequired(): bool
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
        $typedValue = $this->type->getTypedValue($value);
        $this->value->set($typedValue);
    }
}