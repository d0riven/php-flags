<?php


namespace PhpFlags;


class ParsedArgs
{
    /**
     * @var array
     */
    private $args;

    /**
     * ParsedArgs constructor.
     *
     * @param ArgSpec[] $argSpecs
     * @param string[]  $args
     */
    public function __construct(array $argSpecs, array $args)
    {
        $this->args = $args;

        // TODO: args は全部optionalか全部requiredかじゃないと通さないようにする
        $invalidReasons = [];

        if (count($argSpecs) < count($args)) {
            $invalidReasons[] = sprintf('The number of arguments is greater than the argument specs.');
        }

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

        if ($invalidReasons !== []) {
            throw new InvalidSpecException(implode("\n", $invalidReasons));
        }
    }

    /**
     * @param int     $i
     * @param ArgSpec $argSpec
     *
     * @return mixed
     */
    public function getValue(ArgSpec $argSpec, int $i)
    {
//            if ($argSpec->allowMultiple()) {
//                // TODO: ここらへんのコード難しいので整理する
//                // もしargsの指定がない場合でもデフォルト値が取れるようにする
//                $value = $args[$i] ?? $argSpec->getDefault();
//                for ($j = $i; $j < count($args); $j++) {
//                    $value = $args[$j] ?? $argSpec->getDefault();
//                }
//            } else {
        return $this->args[$i] ?? $argSpec->getDefault();
    }
}