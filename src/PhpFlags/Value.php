<?php


namespace PhpFlags;


interface Value
{
    public function get();
    public function set($value);
    public function unsafeSet($values);
    public function type(): Type;
    public function name(): ?string;
}