<?php
declare(strict_types=1);

namespace PhpFlags;


use Closure;

interface MixAppendOption
{
    public function desc(string $describe): MixAppendOption;
    public function default($value): MixAppendOption;
    public function validRule(Closure $validRule): MixAppendOption;
    public function multiple(): MixAppendOption;
}