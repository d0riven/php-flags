<?php


namespace PhpFlags\Spec;


use Closure;

class HelpSpec
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $short;
    /**
     * @var Closure
     */
    private $action;

    public function __construct()
    {
        $this->name = 'help';
        $this->short = 'h';
        $this->action = function ($helpMessage) {
            echo $helpMessage, PHP_EOL;
            exit(0);
        };
    }

    public function action(Closure $action)
    {
        $this->action = $action;

        return $this;
    }

    public function getLong(): string
    {
        return '--' . $this->name;
    }

    public function getShort(): string
    {
        return '-' . $this->short;
    }

    public function hasShort(): bool
    {
        return $this->short !== null;
    }

    public function getAction(): Closure
    {
        return $this->action;
    }
}