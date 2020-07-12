<?php


namespace PhpFlags;


class ParsedFlags
{
    /**
     * @var array
     */
    private $rawFlagCorresponds;
    /**
     * @var array
     */
    private $mergedFlagCorresponds;

    /**
     * @param array      $flagCorresponds
     * @param FlagSpec[] $flagSpecs
     *
     * @return ParsedFlags
     */
    public static function create(array $flagCorresponds, array $flagSpecs)
    {
        $mergedFlagCorresponds = [];
        foreach ($flagSpecs as $flgSpec) {
            $longValues = $flagCorresponds[$flgSpec->getLong()] ?? [];
            $shortValues = $flagCorresponds[$flgSpec->getShort()] ?? [];
            $mergedValues = array_merge($longValues, $shortValues);
            if (count($mergedValues) > 0) {
                $mergedFlagCorresponds[$flgSpec->getLong()] = $mergedValues;
            }
        }

        return new self($flagCorresponds, $mergedFlagCorresponds);
    }


    public function __construct(array $rawFlagCorresponds, array $mergedFlagCorresponds)
    {
        // versionやhelp用。またvalidation時のmessage時出力用として残している
        $this->rawFlagCorresponds = $rawFlagCorresponds;
        $this->mergedFlagCorresponds = $mergedFlagCorresponds;
    }

    public function hasFlag(FlagSpec $flagSpec): bool
    {
        return isset($this->mergedFlagCorresponds[$flagSpec->getLong()]);
    }

    public function hasVersion(?VersionSpec $versionSpec): bool
    {
        return ($versionSpec !== null && (
                array_key_exists($versionSpec->getLong(), $this->rawFlagCorresponds)
                || array_key_exists($versionSpec->getShort(), $this->rawFlagCorresponds)
            ));
    }

    public function hasHelp(HelpSpec $helpSpec): bool
    {
        return array_key_exists($helpSpec->getLong(), $this->rawFlagCorresponds)
            || array_key_exists($helpSpec->getShort(), $this->rawFlagCorresponds);
    }

    /**
     * @param FlagSpec $flagSpec
     *
     * @return mixed
     */
    public function getValue(FlagSpec $flagSpec)
    {
        if ($flagSpec->getType()->equals(Type::BOOL())) {
            return $this->hasFlag($flagSpec);
        } else {
            return $this->mergedFlagCorresponds[$flagSpec->getLong()][0] ?? $flagSpec->getDefault();
        }
    }
}