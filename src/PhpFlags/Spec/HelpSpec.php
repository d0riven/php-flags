<?php


namespace PhpFlags\Spec;

class HelpSpec implements HelpSpecInterface
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
