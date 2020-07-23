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

        $flagNameCounts = [];
        foreach ($flagSpecs as $flagSpec) {
            $flagNameCounts[$flagSpec->getLong()] = $flagNameCounts[$flagSpec->getLong()] ?? 0;
            $flagNameCounts[$flagSpec->getLong()]++;
            if ($flagSpec->hasShort()) {
                $flagNameCounts[$flagSpec->getShort()] = $flagNameCounts[$flagSpec->getShort()] ?? 0;
                $flagNameCounts[$flagSpec->getShort()]++;
            }
        }
        // TODO: Help and version flags are treated the same as other flag specs.
        $flagNameCounts['--help'] = $flagNameCounts['--help'] ?? 0;
        $flagNameCounts['--help']++;
        $flagNameCounts['-h'] = $flagNameCounts['-h'] ?? 0;
        $flagNameCounts['-h']++;
        $flagNameCounts['--version'] = $flagNameCounts['--version'] ?? 0;
        $flagNameCounts['--version']++;
        $flagNameCounts['-v'] = $flagNameCounts['-v'] ?? 0;
        $flagNameCounts['-v']++;
        $duplicateFlagNames = array_filter($flagNameCounts, function ($count) {
            return $count > 1;
        });
        foreach ($duplicateFlagNames as $flagName => $count) {
            $invalidReasons[] = sprintf('duplicate flag name. name:%s, duplicate_count:%d', $flagName, $count);
        }

        // TODO: multiple bool is invalid (unsupported)

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