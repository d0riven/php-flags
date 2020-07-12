<?php
declare(strict_types=1);

namespace PhpFlags;


interface MixAppendOption
{
    public function desc(string $describe): MixAppendOption;

    public function default($value): MixAppendOption;

    public function valid(array $values): MixAppendOption;
}