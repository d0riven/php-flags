<?php


namespace PhpFlags\Spec;


class VersionSpec
{
    /**
     * @var string
     */
    private $version;
    /**
     * @var string
     */
    private $format;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $short;

    public function __construct(string $version)
    {
        $this->version = $version;
        $this->name = 'version';
        $this->short = 'v';
        $this->format = 'version {{VERSION}}';
    }

    public function format(string $format)
    {
        $this->format = $format;
    }

    public function getLong(): string
    {
        return '--' . $this->name;
    }

    public function getShort(): string
    {
        return '-' . $this->short;
    }

    public function hasShort(): bool
    {
        return $this->short !== null;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}