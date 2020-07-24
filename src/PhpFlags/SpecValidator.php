<?php


namespace PhpFlags;


use PhpFlags\Spec\ArgSpecCollection;
use PhpFlags\Spec\FlagSpec;
use PhpFlags\Spec\HelpSpec;
use PhpFlags\Spec\VersionSpec;

class SpecValidator
{
    /**
     * @param FlagSpec[]        $flagSpecs
     * @param HelpSpec          $helpSpec
     * @param VersionSpec|null  $versionSpec
     * @param ArgSpecCollection $argSpecCollection
     */
    public static function validate(array $flagSpecs, HelpSpec $helpSpec, ?VersionSpec $versionSpec, ArgSpecCollection $argSpecCollection)
    {
        $invalidFlagReasons = self::validationFlags($flagSpecs, $helpSpec, $versionSpec);
        $invalidArgReasons = self::validationArgs($argSpecCollection);
        $invalidReasons = array_merge($invalidFlagReasons, $invalidArgReasons);
        if ($invalidReasons !== []) {
            throw new InvalidSpecException(implode("\n", $invalidReasons));
        }
    }

    /**
     * @param FlagSpec[]       $flagSpecs
     * @param HelpSpec         $helpSpec
     * @param VersionSpec|null $versionSpec
     *
     * @return string[]
     */
    public static function validationFlags(array $flagSpecs, HelpSpec $helpSpec, ?VersionSpec $versionSpec)
    {
        $invalidReasons = [];

        foreach ($flagSpecs as $flagSpec) {
            if ($flagSpec->getType()->equals(Type::BOOL()) && $flagSpec->allowMultiple()) {
                $invalidReasons[] = sprintf('bool type is not supported multiple. flag:%s', $flagSpec->getLong());
            }
        }

        $flagNameCounts = [];
        $flagSpecsHelpVersion = ($versionSpec === null) ?
            array_merge($flagSpecs, [$helpSpec]) :
            array_merge($flagSpecs, [$helpSpec, $versionSpec]);
        foreach ($flagSpecsHelpVersion as $flagSpec) {
            $flagNameCounts[$flagSpec->getLong()] = $flagNameCounts[$flagSpec->getLong()] ?? 0;
            $flagNameCounts[$flagSpec->getLong()]++;
            if ($flagSpec->hasShort()) {
                $flagNameCounts[$flagSpec->getShort()] = $flagNameCounts[$flagSpec->getShort()] ?? 0;
                $flagNameCounts[$flagSpec->getShort()]++;
            }
        }
        $duplicateFlagNames = array_filter($flagNameCounts, function ($count) {
            return $count > 1;
        });
        foreach ($duplicateFlagNames as $flagName => $count) {
            $invalidReasons[] = sprintf('duplicate flag name. name:%s, duplicate_count:%d', $flagName, $count);
        }

        return $invalidReasons;
    }

    /**
     * @param ArgSpecCollection $argSpecCollection
     *
     * @return string[]
     */
    public static function validationArgs(ArgSpecCollection $argSpecCollection)
    {
        $invalidReasons = [];

        foreach ($argSpecCollection as $i => $argSpec) {
            if (!$argSpec->allowMultiple()) {
                continue;
            }
            if ($argSpecCollection->count() - 1 !== $i) {
                $invalidReasons[] = sprintf("multiple value option are only allowed for the last argument");
                break;
            }
        }

        if (!$argSpecCollection->isAllRequired() && !$argSpecCollection->isAllOptional()) {
            $invalidReasons[] = sprintf('args should be all of required or optional (cannot mix required and optional args)');
        }

        return $invalidReasons;
    }
}