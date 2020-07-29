<?php
declare(strict_types=1);

namespace PhpFlags\Spec;

class ApplicationSpec
{
    /** @var FlagSpec[] */
    private $flags;
    /** @var ArgSpec[] */
    private $args;
    /** @var VersionSpec|null */
    private $version;
    /** @var HelpSpec */
    private $help;

    public function __construct()
    {
        $this->flags = [];
        $this->args = [];
        $this->version = null;
        $this->help = new HelpSpec();
    }

    public function flag(string $long): FlagSpec
    {
        $flagSpec = new FlagSpec($long);
        $this->flags[] = $flagSpec;

        return $flagSpec;
    }

    public function arg(): ArgSpec
    {
        $argSpec = new ArgSpec();
        $this->args[] = $argSpec;

        return $argSpec;
    }

    public function help(): HelpSpec
    {
        $this->help = new HelpSpec();

        return $this->help;
    }

    public function version(string $version): VersionSpec
    {
        $this->version = new VersionSpec($version);

        return $this->version;
    }

    /**
     * @return FlagSpecCollection
     */
    public function getFlagSpecCollection(): FlagSpecCollection
    {
        return new FlagSpecCollection($this->flags, $this->help, $this->version);
    }

    /**
     * @return ArgSpecCollection
     */
    public function getArgSpecCollection(): ArgSpecCollection
    {
        return new ArgSpecCollection($this->args);
    }

    public function getHelpSpec(): HelpSpec
    {
        return $this->help;
    }

    public function getVersionSpec(): ?VersionSpec
    {
        return $this->version;
    }
}
