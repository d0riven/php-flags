<?php
declare(strict_types=1);

namespace PhpFlags;


interface TypingValue
{
    public function int(): Value;

    public function float(): Value;

    public function bool(): Value;

    public function string(): Value;

    public function date(): Value;
}