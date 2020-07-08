<?php


namespace PhpFlags;

// TODO: こっちにtype持たせる
class Value {
    /**
     * @var mixed|null
     */
    private $value = null;
    /**
     * @var string
     */
    private $name;

    public function __construct(?string $name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed|null
     */
    public function get()
    {
        return $this->value;
    }

    public function name():?string
    {
        return $this->name;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function set($value)
    {
        return $this->value = $value;
    }

}

