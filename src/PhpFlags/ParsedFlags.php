<?php


namespace PhpFlags;


use PhpFlags\Spec\FlagSpec;
use PhpFlags\Spec\FlagSpecCollection;
use PhpFlags\Spec\HelpSpec;
use PhpFlags\Spec\VersionSpec;

class ParsedFlags
{
    /**
     * @var array<array>
     */
    private $rawFlagCorresponds;
    /**
     * @var array<array>
     */
    private $mergedFlagCorresponds;


    /**
     * @param FlagSpecCollection $flagSpecCollection
     * @param array<array>       $rawFlagCorresponds
     *
     */
    public function __construct(FlagSpecCollection $flagSpecCollection, array $rawFlagCorresponds)
    {
        // versionやhelp用。またvalidation時のmessage時出力用として残している
        $this->rawFlagCorresponds = $rawFlagCorresponds;
        $this->mergedFlagCorresponds = $this->mergeShortLong($flagSpecCollection, $rawFlagCorresponds);
    }

    /**
     * @param FlagSpecCollection $flagSpecCollection
     * @param array<array>       $flagCorresponds
     *
     * @return array<array>
     */
    private function mergeShortLong(FlagSpecCollection $flagSpecCollection, array $flagCorresponds)
    {
        $mergedFlagCorresponds = [];
        /** @var FlagSpec $flgSpec */
        foreach ($flagSpecCollection as $flgSpec) {
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
        // multiple
        if ($flagSpec->allowMultiple()) {
            // bool flagの複数値は不要に思うのでサポート外
            return $this->mergedFlagCorresponds[$flagSpec->getLong()] ?? $flagSpec->getDefault();
        }

        // single
        if ($flagSpec->getType()->equals(Type::BOOL())) {
            return $this->hasFlag($flagSpec);
        }

        return $this->mergedFlagCorresponds[$flagSpec->getLong()][0] ?? $flagSpec->getDefault();
    }
}