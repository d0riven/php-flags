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

    public static function create(ApplicationSpec $applicationSpec):Parser
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

        $helpSpec = $this->appSpec->getHelpSpec();
        if (array_key_exists($helpSpec->getLong(), $flagCorresponds) || array_key_exists($helpSpec->getShort(),
                $flagCorresponds)) {
            echo $this->helpGenerator->generate($this->appSpec), PHP_EOL;
            exit(1);
        }

        $versionSpec = $this->appSpec->getVersionSpec();
        if ($versionSpec !== null && (
                array_key_exists($versionSpec->getLong(), $flagCorresponds)
                || array_key_exists($versionSpec->getShort(), $flagCorresponds)
            )
        ) {
            echo $versionSpec->genMessage(), PHP_EOL;
            exit(1);
        }

        // TODO: applyとvalidationは分離させて、helpの判定前にはチェックする
        $this->applyFlagValues($this->mergeLongShort($flagCorresponds));
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

        return [$flagCorresponds, $args];
    }

    private function mergeLongShort(array $flagCorresponds): array
    {
        // このタイミングでSpecにないFlagの対応は削除される
        $mergedFlagCorresponds = [];
        foreach ($this->appSpec->getFlagSpecs() as $flgSpec) {
            $longValues = $flagCorresponds[$flgSpec->getLong()] ?? [];
            $shortValues = $flagCorresponds[$flgSpec->getShort()] ?? [];
            $mergedValues = array_merge($longValues, $shortValues);
            if (count($mergedValues) > 0) {
                $mergedFlagCorresponds[$flgSpec->getLong()] = $mergedValues;
            }
        }

        return $mergedFlagCorresponds;
    }

    private function applyFlagValues(array $flagCorresponds)
    {
        $invalidReasons = [];
        foreach ($this->appSpec->getFlagSpecs() as $flagSpec) {
            $hasOption = isset($flagCorresponds[$flagSpec->getLong()]);
            if (!$hasOption && $flagSpec->getRequired()) {
                $invalidReasons[] = sprintf('required flag. flag:%s', $flagSpec->getLong());
                continue;
            }

            if ($flagSpec->getType()->equals(Type::BOOL())) {
                $value = $hasOption;
            } else {
                $value = $flagCorresponds[$flagSpec->getLong()] ?? $flagSpec->getDefault();
            }
            try {
                $flagSpec->setValue($value);
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