<?php


namespace PhpFlags;

use PhpFlags\Spec\ArgSpec;
use PhpFlags\Spec\ArgSpecCollection;
use PhpFlags\Spec\FlagSpec;
use PhpFlags\Spec\FlagSpecCollection;

class SpecValidator
{
    /**
     * @param FlagSpecCollection $flagSpecCollection
     * @param ArgSpecCollection  $argSpecCollection
     */
    public static function validate(FlagSpecCollection $flagSpecCollection, ArgSpecCollection $argSpecCollection): void
    {
        $invalidFlagReasons = self::validationFlags($flagSpecCollection);
        $invalidArgReasons = self::validationArgs($argSpecCollection);
        $invalidReasons = array_merge($invalidFlagReasons, $invalidArgReasons);
        if ($invalidReasons !== []) {
            throw new InvalidSpecException(implode("\n", $invalidReasons));
        }
    }

    /**
     * @param FlagSpecCollection $flagSpecCollection
     *
     * @return string[]
     */
    public static function validationFlags(FlagSpecCollection $flagSpecCollection): array
    {
        $invalidReasons = [];

        /** @var FlagSpec $flagSpec */
        foreach ($flagSpecCollection as $flagSpec) {
            if ($flagSpec->getType()->equals(Type::BOOL()) && $flagSpec->allowMultiple()) {
                $invalidReasons[] = sprintf('bool type is not supported multiple. flag:%s', $flagSpec->getLong());
            }
        }

        $flagNameCounts = [];
        foreach ($flagSpecCollection->getWithHelpVersion() as $flagSpec) {
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

        // TODO: version format include {{FORMAT}}

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

        /** @var ArgSpec $argSpec */
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
