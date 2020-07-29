<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpFlags\Parser;
use PhpFlags\Spec\ApplicationSpec;

$appSpec = new ApplicationSpec();
// $ php examples/chain/customVersionHelp.php --ver
// app version: 1.0
$appSpec->version('1.0')->long('ver')->short('V')
    ->format('app version: {{VERSION}}')
    ->action(function ($versionMessage) {
        fputs(STDERR, $versionMessage);
        exit(1);
    });
//    $ php examples/chain/customVersionHelp.php  --show-help
// or $ php examples/chain/customVersionHelp.php  -s
// Usage:
// php examples/chain/customVersionHelp.php --example-flag=int [FLAG]... (EXAMPLE-ARG)
//
// FLAG:
//         --example-flag=int
//                 example flag declaration
//
// ARG:
//         EXAMPLE-ARG
//                 example arg declaration
$appSpec->help()->long('show-help')->short('s')
    ->action(function ($helpMessage) {
        fputs(STDERR, $helpMessage);
        exit(1);
    });
$appSpec->flag('example-flag')->desc('example flag declaration')->int();
$appSpec->arg()->desc('example arg declaration')->int('EXAMPLE-ARG');
try {
    Parser::create($appSpec)->parse($argv);
} catch (PhpFlags\InvalidArgumentsException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
} catch (PhpFlags\InvalidSpecException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
}
