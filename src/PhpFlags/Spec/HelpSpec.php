<?php


namespace PhpFlags\Spec;


use Closure;

class HelpSpec
{
    use FlagArgAppendOptionTrait;
    use FlagSpecOptionTrait;

    /**
     * @var Closure
     */
    private $action;

    public function __construct()
    {
        $this->long = 'help';
        $this->short = 'h';
        $this->action = function ($helpMessage) {
            echo $helpMessage, PHP_EOL;
            exit(0);
        };
    }

    public function long(string $long): HelpSpec
    {
        $this->long = $long;

        return $this;
    }

    public function clearShort(): HelpSpec
    {
        $this->short = null;

        return $this;
    }

    public function action(Closure $action): HelpSpec
    {
        $this->action = $action;

        return $this;
    }

    public function getAction(): Closure
    {
        return $this->action;
    }
}