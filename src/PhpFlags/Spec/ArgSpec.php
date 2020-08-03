<?php


namespace PhpFlags\Spec;

class ArgSpec implements ArgSpecInterface
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
