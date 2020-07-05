<?php
declare(strict_types=1);

namespace PhpFlags;

class ApplicationSpec
{
    /** @var FlagSpec[] */
    private $flags;
    /** @var ArgSpec[] */
    private $args;
    /** @var VersionSpec */
    private $version;
    /** @var HelpSpec */
    private $help;

    public function __construct()
    {
        $this->flags = [];
        $this->args = [];
        $this->version = null;
//        $this->help = new HelpSpec($this, 'help');
    }

    public function flag(string $flag): FlagSpec
    {
        $flagSpec = new FlagSpec($flag);
        $this->flags[] = $flagSpec;

        return $flagSpec;
    }

    public function arg(string $name): ArgSpec
    {
        $argSpec = new ArgSpec($name);
        $this->args[] = $argSpec;

        return $argSpec;
    }

    public function version(string $version): VersionSpec
    {
        return new VersionSpec($version);
    }

    /**
     * @return FlagSpec[]
     */
    public function getFlagSpecs(): array
    {
        return $this->flags;
    }

    /**
     * @return ArgSpec[]
     */
    public function getArgSpecs(): array
    {
        return $this->args;
    }

}