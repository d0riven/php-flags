<?php


namespace PhpFlags\Spec;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class ArgSpecCollection implements IteratorAggregate
{
    /**
     * @var ArgSpec[]
     */
    private $argSpecs;

    /**
     * @param ArgSpec[] $argSpecs
     */
    public function __construct(array $argSpecs)
    {
        $this->argSpecs = $argSpecs;
    }

    /**
     * @return Traversable<ArgSpec>
     */
    public function getIterator()
    {
        return new ArrayIterator($this->argSpecs);
    }

    /**
     * @return bool
     */
    public function hasAllowMultiple(): bool
    {
        $hasAllowMultiple = false;
        foreach ($this as $argSpec) {
            if ($argSpec->allowMultiple()) {
                $hasAllowMultiple = true;
            }
        }

        return $hasAllowMultiple;
    }

    public function isAllRequired(): bool
    {
        return array_reduce($this->argSpecs, function ($isAllRequired, $argSpec) {
            /** @var ArgSpec $argSpec */
            return $argSpec->isRequired() && $isAllRequired;
        }, true);
    }

    public function isAllOptional(): bool
    {
        return array_reduce($this->argSpecs, function ($isAllOptional, $argSpec) {
            /** @var ArgSpec $argSpec */
            return !$argSpec->isRequired() && $isAllOptional;
        }, true);
    }

    public function count(): int
    {
        return count($this->argSpecs);
    }
}
