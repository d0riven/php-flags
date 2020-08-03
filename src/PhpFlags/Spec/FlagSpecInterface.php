<?php


namespace PhpFlags\Spec;


use Closure;
use PhpFlags\Value;

interface FlagSpecInterface
{
    public function int(string $valueName = 'int'): Value;

    public function float(string $valueName = 'float'): Value;

    public function bool(): Value;

    public function string(string $valueName = 'string'): Value;

    public function date(string $valueName = 'date'): Value;

    /**
     * Allow multiple option values. (e.g. If -f 1 -f 2 -f 3, get values [1, 2, 3])
     *
     * @return self
     */
    public function multiple();

    /**
     * set flag description
     *
     * @param string $describe
     *
     * @return self
     */
    public function desc(string $describe);

    /**
     * Set default value. If the default value is not specified, it is treated as a required flag (other bool).
     *
     * @param mixed $value
     *
     * @return self
     */
    public function default($value);

    /**
     * Set a callback that throws an exception as an invalid value if false is returned.
     *
     * @param Closure $validRule  Expected callback format is f($value) { return boolean; }
     *
     * @return self
     */
    public function validRule(Closure $validRule);

    /**
     * @param string $short
     *
     * @return self
     */
    public function short(string $short);
}