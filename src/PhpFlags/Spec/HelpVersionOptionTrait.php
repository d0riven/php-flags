<?php


namespace PhpFlags\Spec;

use Closure;

trait HelpVersionOptionTrait
{
    /**
     * @var Closure
     */
    private $action;

    public function long(string $long)
    {
        $this->long = $long;

        return $this;
    }

    public function clearShort()
    {
        $this->short = null;

        return $this;
    }

    /**
     * @param Closure $action expected f($message) { do something }
     */
    public function action(Closure $action)
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): Closure
    {
        return $this->action;
    }
}
