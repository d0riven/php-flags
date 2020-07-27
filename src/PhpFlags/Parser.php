<?php

namespace PhpFlags;

use PhpFlags\Spec\ApplicationSpec;
use PhpFlags\Spec\ArgSpec;
use PhpFlags\Spec\FlagSpecCollection;
use PhpFlags\Spec\VersionSpec;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

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
    public function parse(array $argv): void
    {
        $flagSpecCollection = $this->appSpec->getFlagSpecCollection();
        $helpSpec = $this->appSpec->getHelpSpec();
        $versionSpec = $this->appSpec->getVersionSpec();
        SpecValidator::validate($this->appSpec->getFlagSpecCollection(), $this->appSpec->getArgSpecCollection());

        [$flagCorresponds, $args] = $this->parseArgv($argv, $flagSpecCollection);

        $parsedFlags = new ParsedFlags($flagSpecCollection, $flagCorresponds);
        if ($parsedFlags->hasHelp($helpSpec)) {
            $helpSpec->getAction()($this->helpGenerator->generate($this->appSpec));
        }

        if ($parsedFlags->hasVersion($versionSpec)) {
            $versionSpec->getAction()($this->genVersionMessage($versionSpec));
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
     * @param string[]           $argv
     * @param FlagSpecCollection $flagSpecCollection
     *
     * @return array{0:array<array>,1:array}
     */
    private function parseArgv(array $argv, FlagSpecCollection $flagSpecCollection): array
    {
        // TODO: test parseArgv
        array_shift($argv); // delete script name
        $boolFlagNames = $flagSpecCollection->getBooleanLongShortFlagStrings();

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
        foreach ($this->appSpec->getFlagSpecCollection() as $flagSpec) {
            if (!$parsedFlags->hasFlag($flagSpec) && $flagSpec->isRequired()) {
                $invalidReasons[] = sprintf('required flag. flag:%s', $flagSpec->getLong());
                continue;
            }

            $rawValue = $parsedFlags->getValue($flagSpec);
            try {
                // boolは呼び出し側でbooleanしか渡さないという想定
                $v = $flagSpec->getValue();
                if ($v->type()->equals(TYPE::BOOL())) {
                    $v->unsafeSet($rawValue);
                } else {
                    $v->set($rawValue);
                }
            } catch (InvalidArgumentsException $e) {
                $invalidReasons[] = $e->getMessage();
            }

            $validRule = $flagSpec->getValidRule();
            if ($validRule !== null && !$validRule($rawValue)) {
                $invalidReasons[] = sprintf('invalid by validRule. flag:%s, value:%s', $flagSpec->getLong(), $rawValue);
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

        $argSpecCollection = $this->appSpec->getArgSpecCollection();
        /** @var ArgSpec $argSpec */
        foreach ($argSpecCollection as $i => $argSpec) {
            $value = $parsedArgs->getValue($argSpec, $i);
            try {
                $argSpec->getValue()->set($value);
            } catch (InvalidArgumentsException $e) {
                $invalidReasons[] = $e->getMessage();
            }

            $validRule = $argSpec->getValidRule();
            if ($validRule !== null && !$validRule($value)) {
                $invalidReasons[] = sprintf(
                    'invalid by validRule. argName:%s, value:%s',
                    $argSpec->getName(),
                    $argSpec->allowMultiple() ? sprintf('[%s]', implode(',', $value)) : $value
                );
            }
        }
        if (!($argSpecCollection->hasAllowMultiple()) && $argSpecCollection->count() < $parsedArgs->count()) {
            $invalidReasons[] = sprintf('The number of arguments is greater than the argument specs.');
        }

        return $invalidReasons;
    }

    /**
     * TODO: If the process here is too fat, create a VersionGenerator and move it there.
     *
     * @param VersionSpec $versionSpec
     *
     * @return string
     */
    private function genVersionMessage(VersionSpec $versionSpec): string
    {
        $twig = new Environment(new ArrayLoader(['version' => $versionSpec->getFormat()]));

        return $twig->render('version', ['VERSION' => $versionSpec->getVersion()]);
    }
}
