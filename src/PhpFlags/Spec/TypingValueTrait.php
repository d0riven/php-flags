<?php


namespace PhpFlags\Spec;

use PhpFlags\MultipleValue;
use PhpFlags\SingleValue;
use PhpFlags\Type;
use PhpFlags\Value;

trait TypingValueTrait
{
    /**
     * @var SingleValue|MultipleValue|null
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

    /**
     * Allow multiple option values. (e.g. If -f 1 -f 2 -f 3, get values [1, 2, 3])
     */
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
