<?php
declare(strict_types=1);

namespace PhpFlags;


interface AppendOption
{
    public function desc(string $describe): AppendOption;

    public function short(string $short): AppendOption;

    public function default($value): AppendOption;

    public function valid(array $values): AppendOption;
}