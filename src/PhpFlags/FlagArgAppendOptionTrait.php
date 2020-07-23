<?php


namespace PhpFlags;


use Closure;

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
     * @var bool
     */
    private $multiple;

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
        // TODO: check required

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


    public function validRule(Closure $validRule)
    {
        $this->validRule = $validRule;

        return $this;
    }

    public function getValidRule(): ?Closure
    {
        return $this->validRule;
    }


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