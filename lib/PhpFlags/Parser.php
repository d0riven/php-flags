<?php


namespace PhpFlags;


class Parser
{
    /**
     * @var ApplicationSpec
     */
    private $appSpec;

    public function __construct(ApplicationSpec $spec)
    {
        $this->appSpec = $spec;
    }

    public function parse(array $argv)
    {
        array_shift($argv);
        [$flagCorresponds, $args] = $this->parseArgv($argv);

        // TODO: help, versionチェック

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
            $hasOption = isset($flagSpec->$flagCorresponds[$flagSpec->getLong()]);
            if (!$hasOption && $flagSpec->getRequired()) {
                $invalidReasons[] = sprintf('required flag. flag:%s', $flagSpec->getLong());
                continue;
            }

            if ($flagSpec->getType()->equals(Type::BOOL())) {
                $value = $hasOption;
            } else {
                $value = $flagSpec->$flagCorresponds[$flagSpec->getLong()] ?? $flagSpec->getDefault();
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
        // TODO: Argを複数設定している場合にはmultipleオプションをつけていると落とすようにする
    }
}