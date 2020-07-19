<?php


namespace PhpFlags;


class ParsedArgs
{
    /**
     * @var array
     */
    private $args;

    /**
     * @param ArgSpec[] $argSpecs
     * @param string[]  $args
     *
     * @throws InvalidSpecException
     */
    public function __construct(array $argSpecs, array $args)
    {
        $this->args = $args;

        $this->validation($argSpecs);
    }

    /**
     * @param ArgSpec[] $argSpecs
     *
     * @throws InvalidSpecException
     */
    public function validation(array $argSpecs)
    {
        $invalidReasons = [];

        $hasAllowMultiple = false;
        foreach ($argSpecs as $i => $argSpec) {
            if (!$argSpec->allowMultiple()) {
                continue;
            }
            $hasAllowMultiple = true;
            if (count($argSpecs) - 1 !== $i) {
                $invalidReasons[] = sprintf("multiple value option are only allowed for the last argument");
                break;
            }
        }
        if (!$hasAllowMultiple && count($argSpecs) < count($this->args)) {
            $invalidReasons[] = sprintf('The number of arguments is greater than the argument specs.');
        }

        $isAllRequired = array_reduce($argSpecs, function($isAllRequired, $argSpec) {
            /** @var ArgSpec $argSpec */
            return $argSpec->isRequired() && $isAllRequired;
        }, true);
        $isAllOptional = array_reduce($argSpecs, function($isAllOptional, $argSpec) {
            /** @var ArgSpec $argSpec */
            return !$argSpec->isRequired() && $isAllOptional;
        }, true);
        if (!$isAllRequired && !$isAllOptional) {
            $invalidReasons[] = sprintf('args should be all of required or optional (cannot mix required and optional args)');
        }

        if ($invalidReasons !== []) {
            throw new InvalidSpecException(implode("\n", $invalidReasons));
        }
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

    private function getV(ArgSpec $argSpec, int $i)
    {
        return $this->args[$i] ?? $argSpec->getDefault();
    }
}