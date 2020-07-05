<?php
declare(strict_types=1);

namespace PhpFlags;


interface FlagAppendOption
{
    public function short(string $short): MixAppendOption;
}