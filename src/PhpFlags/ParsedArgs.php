<?php


namespace PhpFlags;


use PhpFlags\Spec\ArgSpec;

class ParsedArgs
{
    /**
     * @var string[]
     */
    private $args;

    /**
     * @param string[] $args
     *
     * @throws InvalidSpecException
     */
    public function __construct(array $args)
    {
        $this->args = $args;
    }

    /**
     * @param int     $i
     * @param ArgSpec $argSpec
     *
     * @return mixed
     */
    public function getValue(ArgSpec $argSpec, int $i)
    {
        if (!$argSpec->allowMultiple()) {
            return $this->getV($argSpec, $i);
        }

        if (!isset($this->args[$i])) {
            return $argSpec->getDefault();
        }

        $values = [];
        for ($j = $i; $j < count($this->args); $j++) {
            $values[] = $this->getV($argSpec, $j);
        }

        return $values;
    }

    private function getV(ArgSpec $argSpec, int $i): string
    {
        return $this->args[$i] ?? $argSpec->getDefault();
    }

    public function count(): int
    {
        return count($this->args);
    }
}