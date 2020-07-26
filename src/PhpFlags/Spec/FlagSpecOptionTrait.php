<?php


namespace PhpFlags\Spec;


trait FlagSpecOptionTrait
{
    /**
     * @var string
     */
    private $long;
    /**
     * @var string|null
     */
    private $short;

    public function short(string $short)
    {
        $this->short = $short;

        return $this;
    }

    public function getLong(): string
    {
        return '--' . $this->long;
    }

    public function getShort(): ?string
    {
        if ($this->short === null) {
            return null;
        }

        return '-' . $this->short;
    }

    public function hasShort(): bool
    {
        return $this->short !== null;
    }
}