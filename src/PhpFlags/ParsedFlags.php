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
     * @param FlagSpec[] $flagSpecs
     * @param array      $rawFlagCorresponds
     *
     * @throws InvalidSpecException
     */
    public function __construct(array $flagSpecs, array $rawFlagCorresponds)
    {
        // versionやhelp用。またvalidation時のmessage時出力用として残している
        $this->rawFlagCorresponds = $rawFlagCorresponds;
        $this->mergedFlagCorresponds = $this->mergeShortLong($flagSpecs, $rawFlagCorresponds);

        $this->validation($flagSpecs);
    }

    /**
     * @param FlagSpec[] $flagSpecs
     *
     * @throws InvalidSpecException
     */
    public function validation(array $flagSpecs)
    {
        $invalidReasons = [];
        foreach ($flagSpecs as $flagSpec) {
            if (!$this->hasFlag($flagSpec) && $flagSpec->getRequired()) {
                $invalidReasons[] = sprintf('required flag. flag:%s', $flagSpec->getLong());
            }
        }

        if ($invalidReasons !== []) {
            throw new InvalidSpecException(implode("\n", $invalidReasons));
        }
    }

    /**
     * @param FlagSpec[] $flagSpecs
     * @param array      $flagCorresponds
     *
     * @return array
     */
    private function mergeShortLong(array $flagSpecs, array $flagCorresponds)
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

        return $mergedFlagCorresponds;
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