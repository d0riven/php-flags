<?php


namespace PhpFlags;


class Parser
{
    /**
     * @var ApplicationSpec
     */
    private $appSpec;
    /**
     * @var HelpGenerator
     */
    private $helpGenerator;

    public static function create(ApplicationSpec $applicationSpec): Parser
    {
        return new self($applicationSpec, new HelpGenerator($_SERVER['SCRIPT_NAME']));
    }

    public function __construct(ApplicationSpec $spec, HelpGenerator $helpGenerator)
    {
        $this->appSpec = $spec;
        $this->helpGenerator = $helpGenerator;
    }

    /**
     * @param string[] $argv
     *
     * @throws InvalidSpecException
     * @throws InvalidArgumentsException
     */
    public function parse(array $argv)
    {
        $flagSpecs = $this->appSpec->getFlagSpecs();
        SpecValidator::validate($flagSpecs, $this->appSpec->getArgSpecs());

        array_shift($argv);
        [$flagCorresponds, $args] = $this->parseArgv($argv, $flagSpecs);

        $parsedFlags = new ParsedFlags($flagSpecs, $flagCorresponds);
        if ($parsedFlags->hasHelp($this->appSpec->getHelpSpec())) {
            echo $this->helpGenerator->generate($this->appSpec), PHP_EOL;
            exit(1);
        }

        if ($parsedFlags->hasVersion($this->appSpec->getVersionSpec())) {
            echo $this->appSpec->getVersionSpec()->genMessage(), PHP_EOL;
            exit(1);
        }

        $flagInvalidReasons = $this->applyFlagValues($parsedFlags);
        $parsedArgs = new ParsedArgs($args);
        $argInvalidReasons = $this->applyArgValues($parsedArgs);
        $invalidReasons = array_merge($flagInvalidReasons, $argInvalidReasons);
        if ($invalidReasons !== []) {
            throw new InvalidArgumentsException(implode("\n", $invalidReasons));
        }
    }

    /**
     * @param string[]   $argv
     * @param FlagSpec[] $flagSpecs
     *
     * @return array
     */
    private function parseArgv(array $argv, array $flagSpecs): array
    {
        $boolFlagLongNames = array_map(function ($flagSpec) {
            return $flagSpec->getLong();
        }, array_filter($flagSpecs, function ($flagSpec) {
            return $flagSpec->getType()->equals(Type::BOOL());
        }));
        $boolFlagShortNames = array_map(function ($flagSpec) {
            return $flagSpec->getShort();
        }, array_filter($flagSpecs, function ($flagSpec) {
            return $flagSpec->getType()->equals(Type::BOOL()) && $flagSpec->getShort() !== null;
        }));
        $boolFlagNames = array_merge($boolFlagLongNames, $boolFlagShortNames);

        $flagCorresponds = [];
        $args = [];
        $argc = count($argv);
        for ($i = 0; $i < $argc; $i++) {
            $cur = $argv[$i];
            // is flag
            if (substr($cur, 0, 1) === '-') {
                $sepPos = strpos($cur, '=');
                if ($sepPos === false) {
                    $curKey = $cur;
                    // next is EOA(end of args)
                    if (!isset($argv[$i + 1])) {
                        $flagCorresponds[$curKey][] = null;
                        continue;
                    }
                    $next = $argv[$i + 1];
                    // next is option or this flag type of bool
                    if (substr($next, 0, 1) === '-'
                        || in_array($curKey, $boolFlagNames, true)) {
                        $flagCorresponds[$curKey][] = null;
                        continue;
                    }
                    // next is value
                    $flagCorresponds[$curKey][] = $next;
                    // shift next arg
                    $i++;
                    continue;
                }

                [$name, $value] = explode('=', $cur, 2);
                $flagCorresponds[$name][] = $value;

                continue;
            }

            // is arg
            while (isset($argv[$i]) && substr($argv[$i], 0, 1) !== '-') {
                $args[] = $argv[$i];
                $i++;
            }
        }

        return [$flagCorresponds, $args];
    }

    /**
     * @param ParsedFlags $parsedFlags
     *
     * @return string[]
     */
    private function applyFlagValues(ParsedFlags $parsedFlags): array
    {
        $invalidReasons = [];
        foreach ($this->appSpec->getFlagSpecs() as $flagSpec) {
            if (!$parsedFlags->hasFlag($flagSpec) && $flagSpec->isRequired()) {
                $invalidReasons[] = sprintf('required flag. flag:%s', $flagSpec->getLong());
                continue;
            }

            $value = $parsedFlags->getValue($flagSpec);
            try {
                $flagSpec->setValue($value);
            } catch (InvalidArgumentsException $e) {
                $invalidReasons[] = $e->getMessage();
            }

            $validRule = $flagSpec->getValidRule();
            if ($validRule !== null && !$validRule($value)) {
                $invalidReasons[] = sprintf('invalid by validRule. flag:%s, value:%s', $flagSpec->getLong(), $value);
            }
        }

        return $invalidReasons;
    }

    /**
     * @param ParsedArgs $parsedArgs
     *
     * @return string[]
     */
    private function applyArgValues(ParsedArgs $parsedArgs): array
    {
        $invalidReasons = [];

        $argSpecs = $this->appSpec->getArgSpecs();
        $hasAllowMultiple = false;
        foreach ($argSpecs as $i => $argSpec) {
            if ($argSpec->allowMultiple()) {
                // TODO: move to ArgSpecCollection hasAllowMultiple()
                $hasAllowMultiple = true;
            }
            $value = $parsedArgs->getValue($argSpec, $i);
            try {
                $argSpec->setValue($value);
            } catch (InvalidArgumentsException $e) {
                $invalidReasons[] = $e->getMessage();
            }

            $validRule = $argSpec->getValidRule();
            if ($validRule !== null && !$validRule($value)) {
                $invalidReasons[] = sprintf('invalid by validRule. argName:%s, value:%s', $argSpec->getName(), $value);
            }
        }
        if (!$hasAllowMultiple && count($argSpecs) < $parsedArgs->count()) {
            $invalidReasons[] = sprintf('The number of arguments is greater than the argument specs.');
        }

        return $invalidReasons;
    }
}