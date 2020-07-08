<?php
declare(strict_types=1);

namespace PhpFlags;

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

    // TODO: delete
    public static function create():ApplicationSpec
    {
        return new self();
    }

    public function __construct()
    {
        $this->flags = [];
        $this->args = [];
        $this->version = null;
        $this->help = new HelpSpec();
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
        $this->version = new VersionSpec($version);

        return $this->version;
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

    public function getHelpSpec(): HelpSpec
    {
        return $this->help;
    }

    public function getVersionSpec(): ?VersionSpec
    {
        return $this->version;
    }
}