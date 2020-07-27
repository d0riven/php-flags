<?php


namespace PhpFlags;


interface Value
{
    /**
     * @return mixed
     */
    public function get();
    /**
     * @param mixed $value
     */
    public function set($value):void;
    /**
     * @param mixed $value
     */
    public function unsafeSet($value):void;
    public function type(): Type;
    public function name(): ?string;
}