<?php


namespace PhpFlags;

class SingleValue implements Value
{
    /**
     * @var mixed|null
     */
    private $value = null;
    /**
     * @var Type
     */
    private $type;
    /**
     * @var string|null
     */
    private $name;

    public function __construct(Type $type, ?string $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * @return mixed|null return null if call get() before parse
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function set($value): void
    {
        assert(!is_array($value));
        $this->value = $this->type->getTypedValue($value);
    }

    /**
     * @param mixed $value
     */
    public function unsafeSet($value): void
    {
        assert(!is_array($value));
        $this->value = $value;
    }

    public function type(): Type
    {
        return $this->type;
    }

    public function name(): ?string
    {
        return $this->name;
    }
}

