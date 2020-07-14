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
        // TODO: args は全部optionalか全部requiredかじゃないと通さないようにする
        $invalidReasons = [];

        if (count($argSpecs) < count($this->args)) {
            $invalidReasons[] = sprintf('The number of arguments is greater than the argument specs.');
        }

        foreach ($argSpecs as $i => $argSpec) {
            if (!$argSpec->allowMultiple()) {
                continue;
            }
            if (count($argSpecs) - 1 !== $i) {
                $invalidReasons[] = sprintf("multiple value option are only allowed for the last argument");
                break;
            }
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

        if (!isset($args[$i])) {
            return $argSpec->getDefault();
        }

        $values = [];
        for ($j = $i; $j < count($args); $j++) {
            $values[] = $this->getV($argSpec, $j);
        }
    }

    private function getV(ArgSpec $argSpec, int $i)
    {
        return $this->args[$i] ?? $argSpec->getDefault();
    }
}