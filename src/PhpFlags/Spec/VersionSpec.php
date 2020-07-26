<?php


namespace PhpFlags\Spec;


use Closure;

class VersionSpec
{
    use FlagArgAppendOptionTrait;
    use FlagSpecOptionTrait;
    use HelpVersionOptionTrait;

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
        $this->long = 'version';
        $this->short = 'v';
        $this->format = 'version {{VERSION}}';

        $this->action = function ($versionMessage) {
            echo $versionMessage, PHP_EOL;
            exit(0);
        };
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
}