<?php


namespace PhpFlags\Spec;


use ArrayIterator;
use IteratorAggregate;
use PhpFlags\Type;
use Traversable;

class FlagSpecCollection implements IteratorAggregate
{
    /**
     * @var FlagSpec[]
     */
    private $flagSpecs;
    /**
     * @var HelpSpec
     */
    private $helpSpec;
    /**
     * @var VersionSpec|null
     */
    private $versionSpec;

    /**
     * @param FlagSpec[]       $flagSpecs
     * @param HelpSpec         $helpSpec
     * @param VersionSpec|null $versionSpec
     */
    public function __construct(array $flagSpecs, HelpSpec $helpSpec, ?VersionSpec $versionSpec)
    {
        $this->flagSpecs = $flagSpecs;
        $this->helpSpec = $helpSpec;
        $this->versionSpec = $versionSpec;
    }

    /**
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->flagSpecs);
    }

    /**
     * @return string[]
     */
    public function getBooleanLongShortFlagStrings(): array
    {
        $boolFlagLongNames = array_map(function ($flagSpec) {
            return $flagSpec->getLong();
        }, array_filter($this->flagSpecs, function ($flagSpec) {
            return $flagSpec->getType()->equals(Type::BOOL());
        }));
        $boolFlagShortNames = array_map(function ($flagSpec) {
            return $flagSpec->getShort();
        }, array_filter($this->flagSpecs, function ($flagSpec) {
            return $flagSpec->getType()->equals(Type::BOOL()) && $flagSpec->getShort() !== null;
        }));
        return array_merge($boolFlagLongNames, $boolFlagShortNames);
    }

    public function getWithHelpVersion()
    {
        return ($this->versionSpec === null) ?
            array_merge($this->flagSpecs, [$this->helpSpec]) :
            array_merge($this->flagSpecs, [$this->helpSpec, $this->versionSpec]);
    }
}