<?php


namespace PhpFlags;


class SpecValidator
{
    /**
     * @param FlagSpec[] $flagSpecs
     * @param ArgSpec[] $argSpecs
     */
    public static function validate(array $flagSpecs, array $argSpecs)
    {
        $invalidFlagReasons = self::validationFlags($flagSpecs);
        $invalidArgReasons = self::validationArgs($argSpecs);
        $invalidReasons = array_merge($invalidFlagReasons, $invalidArgReasons);
        if ($invalidReasons !== []) {
            throw new InvalidSpecException(implode("\n", $invalidReasons));
        }
    }

    /**
     * @param FlagSpec[] $flagSpecs
     *
     * @return string[]
     */
    public static function validationFlags(array $flagSpecs)
    {
        $invalidReasons = [];

        // TODO: The data acquisition for each validation is moved to the FlagSpecCollection.
        foreach ($flagSpecs as $flagSpec) {
            if ($flagSpec->getType()->equals(Type::BOOL()) && $flagSpec->allowMultiple()) {
                $invalidReasons[] = sprintf('bool type is not supported multiple. flag:%s', $flagSpec->getLong());
            }
        }

        $flagNames = [];
        foreach ($flagSpecs as $flagSpec) {
            $flagNames[$flagSpec->getLong()] = $flagNames[$flagSpec->getLong()] ?? 0;
            $flagNames[$flagSpec->getLong()]++;
            if ($flagSpec->hasShort()) {
                $flagNames[$flagSpec->getShort()] = $flagNames[$flagSpec->getShort()] ?? 0;
                $flagNames[$flagSpec->getShort()]++;
            }
        }
        // TODO: Help and version flags are treated the same as other flag specs.
        $flagNames['help'] = $flagNames['help'] ?? 0;
        $flagNames['help']++;
        $flagNames['h'] = $flagNames['h'] ?? 0;
        $flagNames['h']++;
        $flagNames['version'] = $flagNames['version'] ?? 0;
        $flagNames['version']++;
        $flagNames['v'] = $flagNames['v'] ?? 0;
        $flagNames['v']++;
        $duplicateFlagNames = array_filter($flagNames, function ($count) {
            return $count > 1;
        });
        foreach ($duplicateFlagNames as $flagName => $count) {
            $invalidReasons[] = sprintf('duplicate flag name. name:%s, duplicate_count:%d', $flagName, $count);
        }

        return $invalidReasons;
    }

    /**
     * @param ArgSpec[] $argSpecs
     *
     * @return string[]
     */
    public static function validationArgs(array $argSpecs)
    {
        $invalidReasons = [];

        foreach ($argSpecs as $i => $argSpec) {
            if (!$argSpec->allowMultiple()) {
                continue;
            }
            if (count($argSpecs) - 1 !== $i) {
                $invalidReasons[] = sprintf("multiple value option are only allowed for the last argument");
                break;
            }
        }

        $isAllRequired = array_reduce($argSpecs, function($isAllRequired, $argSpec) {
            /** @var ArgSpec $argSpec */
            return $argSpec->isRequired() && $isAllRequired;
        }, true);
        $isAllOptional = array_reduce($argSpecs, function($isAllOptional, $argSpec) {
            /** @var ArgSpec $argSpec */
            return !$argSpec->isRequired() && $isAllOptional;
        }, true);
        if (!$isAllRequired && !$isAllOptional) {
            $invalidReasons[] = sprintf('args should be all of required or optional (cannot mix required and optional args)');
        }

        return $invalidReasons;
    }
}