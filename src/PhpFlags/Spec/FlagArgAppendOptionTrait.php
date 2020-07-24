<?php


namespace PhpFlags\Spec;


use Closure;
use PhpFlags\Type;

trait FlagArgAppendOptionTrait
{
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

    public function desc(string $describe)
    {
        $this->description = $describe;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function hasDescription(): bool
    {
        return $this->description !== null;
    }


    public function default($value)
    {
        $this->defaultValue = $value;

        return $this;
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

    public function isRequired(): bool
    {
        return !$this->getType()->equals(Type::BOOL()) && $this->defaultValue === null;
    }

    public function validRule(Closure $validRule)
    {
        $this->validRule = $validRule;

        return $this;
    }

    public function getValidRule(): ?Closure
    {
        return $this->validRule;
    }
}