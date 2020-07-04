<?php


namespace PhpFlags;


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

    public function __construct(string $version)
    {
        $this->version = $version;
        $this->format = 'version {{VERSION}}';
    }

    public function format(string $format)
    {
        $this->format = $format;
    }
}