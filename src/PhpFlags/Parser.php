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

    public function parse(array $argv)
    {
        array_shift($argv);
        [$flagCorresponds, $args] = $this->parseArgv($argv);

        $parsedFlags = ParsedFlags::create($flagCorresponds, $this->appSpec->getFlagSpecs());
        if ($parsedFlags->hasHelp($this->appSpec->getHelpSpec())) {
            echo $this->helpGenerator->generate($this->appSpec), PHP_EOL;
            exit(1);
        }

        if ($parsedFlags->hasVersion($this->appSpec->getVersionSpec())) {
            echo $this->appSpec->getVersionSpec()->genMessage(), PHP_EOL;
            exit(1);
        }

        // TODO: applyとvalidationは分離させて、helpの判定前にはチェックする
        $this->applyFlagValues($parsedFlags);
        $this->applyArgValues($args);
    }

    private function parseArgv($argv): array
    {
        $flagCorresponds = [];
        $args = [];
        $argc = count($argv);
        for ($i = 0; $i < $argc; $i++) {
            $cur = $argv[$i];
            // is flag
            if (substr($cur, 0, 1) === '-') {
                $sepPos = strpos($cur, '=');
                if ($sepPos === false) {
                    // next is EOA(end of args)
                    if (!isset($argv[$i + 1])) {
                        $flagCorresponds[$cur][] = null;
                        continue;
                    }
                    $next = $argv[$i + 1];
                    // next is option
                    if (substr($next, 0, 1) === '-') {
                        $flagCorresponds[$cur][] = null;
                        continue;
                    }
                    // next is value
                    $flagCorresponds[$cur][] = $next;
                    // shift next arg
                    $i++;
                    continue;
                }

                [$name, $value] = explode('=', $cur, 1);
                $flagCorresponds[$name][] = $value;

                continue;
            }

            // is arg
            while (isset($argv[$i]) && substr($argv[$i], 0, 1) !== '-') {
                $args[] = $argv[$i];
                $i++;
            }
        }

        // TODO: flagCorrespondsのオブジェクト作成する
        return [$flagCorresponds, $args];
    }

    private function applyFlagValues(ParsedFlags $parsedFlags)
    {
        $invalidReasons = [];
        foreach ($this->appSpec->getFlagSpecs() as $flagSpec) {
            if (!$parsedFlags->hasFlag($flagSpec) && $flagSpec->getRequired()) {
                $invalidReasons[] = sprintf('required flag. flag:%s', $flagSpec->getLong());
                continue;
            }

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

    private function applyArgValues(array $args)
    {
        $invalidReasons = [];

        $argSpecs = $this->appSpec->getArgSpecs();
        // TODO: multiple args
//        foreach ($argSpecs as $i => $argSpec) {
//            if (!$argSpec->allowMultiple()) {
//                continue;
//            }
//            if (count($argSpecs) - 1 !== $i) {
//                $invalidReasons[] = sprintf("multiple value option are only allowed for the last argument");
//                break;
//            }
//        }

        // TODO: args は全部optionalか全部requiredかじゃないと通さないようにする

        if (count($argSpecs) < count($args)) {
            $invalidReasons[] = sprintf('The number of arguments is greater than the argument specs.');
        }

        foreach ($argSpecs as $i => $argSpec) {
//            if ($argSpec->allowMultiple()) {
//                // TODO: ここらへんのコード難しいので整理する
//                // もしargsの指定がない場合でもデフォルト値が取れるようにする
//                $value = $args[$i] ?? $argSpec->getDefault();
//                for ($j = $i; $j < count($args); $j++) {
//                    $value = $args[$j] ?? $argSpec->getDefault();
//                }
//            } else {
            $value = $args[$i] ?? $argSpec->getDefault();
            try {
                $argSpec->setValue($value);
            } catch (InvalidArgumentsException $e) {
                $invalidReasons[] = $e->getMessage();
            }
        }

        if ($invalidReasons !== []) {
            throw new InvalidArgumentsException(implode("\n", $invalidReasons));
        }
    }
}