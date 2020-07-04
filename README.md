# PHP Flags

This library is a parser of command line arguments that can be used sensibly without looking at the documentation.

## Feature

* Explicit declarations.
* Simple Notation.
* Support for required, optional and multiple values.
* Automatic generation of usage.

## Usage

```php
<?php

use PhpFlags\InvalidArgumentsException;
use PhpFlags\ApplicationSpec;

$spec = new ApplicationSpec($argv);
// likely gnu date command option
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
        ->default((new DateTimeImmutable)->format('Y-m-d'))
        ->date();
    $isDebug = $spec->flag('debug')
        ->desc('annotate  the  parsed  date,  and  warn about questionable usage to stderr')
        ->default(false)
        ->bool();
    $iso8601FmtType = $spec->flag('iso-8601')
        ->desc("output date/time in ISO 8601 format.  FMT='date' for date only (the
              default),  'hours', 'minutes', 'seconds', or 'ns' for date and time
              to the indicated precision.  Example: 2006-08-14T02:34:56-06:00")
        ->short('I')
        ->default('date')
        ->valids(['date', 'hours', 'minutes', 'seconds', 'ns'])
        ->string();
// omission ...
    $format = $spec->arg('FORMAT')
        ->desc('too long description... omission')
        ->default('%a %b %e %T %Z %Y')
        ->string();

    $parsed = $spec->parse();
} catch (PhpFlags\InvalidArgumentsException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
}

if ($parsed->existsHelp()) {
    $parsed->showHelp();

    return;
}

if ($parsed->existsVersion()) {
    $parsed->showVersion();

    return;
}
```