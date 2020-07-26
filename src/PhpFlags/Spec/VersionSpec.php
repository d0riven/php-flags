<?php


namespace PhpFlags\Spec;


use Closure;

class VersionSpec
{
    use FlagArgAppendOptionTrait;
    use FlagSpecOptionTrait;

    /**
     * @var string
     */
    private $version;
    /**
     * @var string
     */
    private $format;
    /**
     * @var Closure
     */
    private $action;

    public function __construct(string $version)
    {
        $this->version = $version;
        $this->long = 'version';
        $this->short = 'v';
        $this->format = 'version {{VERSION}}';

        $this->action = function ($versionMessage) {
            echo $versionMessage, PHP_EOL;
            exit(0);
        };
    }

    public function action(Closure $action): VersionSpec
    {
        $this->action = $action;

        return $this;
    }

    public function long(string $long): VersionSpec
    {
        $this->long = $long;

        return $this;
    }

    public function clearShort(): VersionSpec
    {
        $this->short = null;

        return $this;
    }


    public function format(string $format): VersionSpec
    {
        $this->format = $format;

        return $this;
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