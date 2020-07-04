<?php


namespace PhpFlags;


class ArgSpec
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $description;
    /**
     * @var mixed|null
     */
    private $default;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->description = null;
        $this->default = null;
    }

    public function desc(string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function default($default)
    {
        $this->default = $default;

        return $this;
    }

    public function string()
    {
        return 'test';
    }
}