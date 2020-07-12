<?php
declare(strict_types=1);

namespace PhpFlags;


// TODO: argの方にvalueName要らないのでinterfaceは別にする
interface TypingValue
{
    public function int(string $valueName): Value;

    public function float(string $valueName): Value;

    public function bool(): Value;

    public function string(string $valueName): Value;

    public function date(string $valueName): Value;
}