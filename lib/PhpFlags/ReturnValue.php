<?php
declare(strict_types=1);

namespace PhpFlags;


use DateTimeImmutable;

// TODO: こいつ名前が微妙そう（何を返すんだ？って感じだし
interface ReturnValue
{
    public function int(): Value;

    public function float(): Value;

    public function bool(): Value;

    public function string(): Value;

    public function date(): Value;
}