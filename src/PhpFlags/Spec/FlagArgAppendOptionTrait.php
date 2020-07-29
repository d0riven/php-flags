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

    /**
     * set flag description
     *
     * @param string $describe
     */
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

    /**
     * Set default value. If the default value is not specified, it is treated as a required flag (other bool).
     *
     * @param mixed $value
     */
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

    /**
     * Set a callback that throws an exception as an invalid value if false is returned.
     *
     * @param Closure $validRule  Expected callback format is f($value) { return boolean; }
     */
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
