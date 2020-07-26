<?php


namespace PhpFlags\Spec;


use Closure;

class HelpSpec
{
    use FlagArgAppendOptionTrait;
    use FlagSpecOptionTrait;
    use HelpVersionOptionTrait;

    public function __construct()
    {
        $this->long = 'help';
        $this->short = 'h';
        $this->action = function ($helpMessage) {
            echo $helpMessage, PHP_EOL;
            exit(0);
        };
    }

}