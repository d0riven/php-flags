<?php
declare(strict_types=1);

namespace PhpFlags;


// TODO: $valueNameはデフォルト値を用意しておく
interface TypingValue
{
    public function int(string $valueName): Value;
    public function float(string $valueName): Value;
    public function string(string $valueName): Value;
    public function date(string $valueName): Value;
    public function bool(): Value;
}