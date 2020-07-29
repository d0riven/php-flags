<?php


namespace PhpFlags;

class MultipleValue implements Value
{
    /**
     * @var array
     */
    private $values;
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
        $this->values = [];
    }

    /**
     * @return array|null return null if call get() before parse
     */
    public function get()
    {
        return $this->values;
    }

    /**
     * @param array $value
     */
    public function set($value): void
    {
        assert(is_array($value));

        $typedValues = [];
        foreach ($value as $v) {
            $typedValues[] = $this->type->getTypedValue($v);
        }
        $this->values = $typedValues;
    }

    /**
     * @param array $value
     */
    public function unsafeSet($value): void
    {
        assert(is_array($value));
        $this->values = $value;
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
