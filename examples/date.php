<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PhpFlags\ApplicationSpec;
use PhpFlags\Parser;

$spec = new ApplicationSpec();
$versionTextFormat = <<<VERSION
date (GNU coreutils) {{VERSION}}
Copyright (C) 2019 Free Software Foundation, Inc.
License GPLv3+: GNU GPL version 3 or later <https://gnu.org/licenses/gpl.html>.
This is free software: you are free to change and redistribute it.
There is NO WARRANTY, to the extent permitted by law.

Written by David MacKenzie.
VERSION;
$spec->version('8.31')->format($versionTextFormat);
try {
    $date = $spec->flag('date')
        ->desc("display time described by STRING, not 'now'")
        ->short('d')
        ->default(new DateTimeImmutable)
        ->date('STRING');
    $isDebug = $spec->flag('debug')
        ->desc('annotate  the  parsed  date,  and  warn about questionable usage to stderr')
        ->default(false)
        ->bool();

    $iso8601FmtType = $spec->flag('iso-8601')
        ->desc("output date/time in ISO 8601 format.  FMT='date' for date only (the default),  'hours', 'minutes', 'seconds', or 'ns' for date and time to the indicated precision.  Example: 2006-08-14T02:34:56-06:00")
        ->short('I')
        ->default('date')
        ->valid(['date', 'hours', 'minutes', 'seconds', 'ns'])
        ->string('FMT');
// omission ...
    $format = $spec->arg('FORMAT')
        ->desc('too long description... omission')
        ->default('%a %b %e %T %Z %Y')
        ->string('FORMAT');

    $parser = Parser::create($spec);
    $parser->parse($argv);
} catch (PhpFlags\InvalidArgumentsException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
} catch (PhpFlags\InvalidSpecException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
}

var_dump($date->get());
var_dump($isDebug->get());
var_dump($iso8601FmtType->get());
var_dump($format->get());
