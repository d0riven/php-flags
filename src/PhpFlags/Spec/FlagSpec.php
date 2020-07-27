<?php
declare(strict_types=1);


namespace PhpFlags\Spec;


class FlagSpec
{
    use FlagArgAppendOptionTrait;
    use TypingValueTrait;
    use FlagSpecOptionTrait;

    public function __construct(string $long)
    {
        $this->long = $long;
        $this->description = null;
        $this->short = null;
        $this->defaultValue = null;
        $this->validRule = null;
        $this->multiple = false;
        $this->value = null;
    }
}