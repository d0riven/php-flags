<?php


namespace PhpFlags\Spec;


use Closure;

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
    /**
     * @var Closure
     */
    private $action;

    public function __construct(string $version)
    {
        $this->version = $version;
        $this->name = 'version';
        $this->short = 'v';
        $this->format = 'version {{VERSION}}';

        $this->action = function ($versionMessage) {
            echo $versionMessage, PHP_EOL;
            exit(0);
        };
    }

    public function action(Closure $action)
    {
        $this->action = $action;

        return $this;
    }

    public function format(string $format)
    {
        $this->format = $format;

        return $this;
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

    public function getAction(): Closure
    {
        return $this->action;
    }
}