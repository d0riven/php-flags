<?php


namespace PhpFlags\Spec;


use PhpFlags\TypingValueTrait;

class ArgSpec
{
    use FlagArgAppendOptionTrait;
    use TypingValueTrait;

    public function __construct()
    {
        $this->description = null;
        $this->defaultValue = null;
        $this->validRule = null;
        $this->multiple = false;
        $this->type = null;
        $this->value = null;
    }
}