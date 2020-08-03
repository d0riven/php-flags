<?php


namespace PhpFlags\Spec;

use Closure;

interface HelpSpecInterface
{
    /**
     * @param string $short
     *
     * @return self
     */
    public function short(string $short);

    /**
     * @param string $long
     *
     * @return self
     */
    public function long(string $long);

    /**
     * @return self
     */
    public function clearShort();

    /**
     * @param Closure $action expected f($message) { do something }
     *
     * @return self
     */
    public function action(Closure $action);
}
