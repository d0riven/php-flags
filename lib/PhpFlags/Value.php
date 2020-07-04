<?php


namespace PhpFlags;

class Value {
    /**
     * @var mixed|null
     */
    private $value = null;

    /**
     * @return mixed|null
     */
    public function get()
    {
        return $this->value;
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

