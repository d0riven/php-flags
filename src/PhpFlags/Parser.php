<?php


namespace PhpFlags;


use Closure;

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
     * @param array $argv
     *
     * @throws InvalidSpecException
     * @throws InvalidArgumentsException
     */
    public function parse(array $argv)
    {
        array_shift($argv);
        $flagSpecs = $this->appSpec->getFlagSpecs();
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

        $this->applyFlagValues($parsedFlags);
        $parsedArgs = new ParsedArgs($this->appSpec->getArgSpecs(), $args);
        $this->applyArgValues($parsedArgs);
    }

    /**
     * @param array $argv
     * @param FlagSpec[] $flagSpecs
     *
     * @return array
     */
    private function parseArgv(array $argv, array $flagSpecs): array
    {
        $boolFlagLongNames = array_map(function($flagSpec) {
            return $flagSpec->getLong();
        }, array_filter($flagSpecs, function($flagSpec) {
            return $flagSpec->getType()->equals(Type::BOOL());
        }));
        $boolFlagShortNames = array_map(function($flagSpec) {
            return $flagSpec->getShort();
        }, array_filter($flagSpecs, function($flagSpec) {
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
                        || in_array($curKey, $boolFlagLongNames, true)
                        || in_array($curKey, $boolFlagShortNames, true)) {
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

    private function applyFlagValues(ParsedFlags $parsedFlags)
    {
        $invalidReasons = [];
        foreach ($this->appSpec->getFlagSpecs() as $flagSpec) {
            try {
                $flagSpec->setValue($parsedFlags->getValue($flagSpec));
            } catch (InvalidArgumentsException $e) {
                $invalidReasons[] = $e->getMessage();
            }
        }

        if ($invalidReasons !== []) {
            throw new InvalidArgumentsException(implode("\n", $invalidReasons));
        }
    }

    private function applyArgValues(ParsedArgs $parsedArgs)
    {
        $invalidReasons = [];

        foreach ($this->appSpec->getArgSpecs() as $i => $argSpec) {
            try {
                $argSpec->setValue($parsedArgs->getValue($argSpec, $i));
            } catch (InvalidArgumentsException $e) {
                $invalidReasons[] = $e->getMessage();
            }
        }

        if ($invalidReasons !== []) {
            throw new InvalidArgumentsException(implode("\n", $invalidReasons));
        }
    }
}