<?php


use PhpFlags\ApplicationSpec;
use PhpFlags\HelpGenerator;
use PHPUnit\Framework\TestCase;

class HelpGeneratorTest extends TestCase
{

    /**
     * @test
     */
    public function generate()
    {
        $appSpec = new ApplicationSpec();

        $versionTextFormat = <<<VERSION
date (GNU coreutils) {{VERSION}}
Copyright (C) 2019 Free Software Foundation, Inc.
License GPLv3+: GNU GPL version 3 or later <https://gnu.org/licenses/gpl.html>.
This is free software: you are free to change and redistribute it.
There is NO WARRANTY, to the extent permitted by law.

Written by David MacKenzie.
VERSION;
        $appSpec->version('8.31')->format($versionTextFormat);
        $appSpec->flag('date')
            ->desc("display time described by STRING, not 'now'")
            ->short('d')
            ->default(new DateTimeImmutable)
            ->date('STRING');
        $appSpec->flag('debug')
            ->desc('annotate the parsed date, and warn about questionable usage to stderr')
            ->default(false)
            ->bool();
        $appSpec->flag('iso-8601')
            ->desc("output date/time in ISO 8601 format. FMT='date' for date only (the default), 'hours', 'minutes', 'seconds', or 'ns' for date and time to the indicated precision. Example: 2006-08-14T02:34:56-06:00")
            ->short('I')
            ->default('date')
            ->validRule(function ($value) {
                return in_array($value, ['date', 'hours', 'minutes', 'seconds', 'ns'], true);
            })
            ->string('FMT');

        $appSpec->arg()
            ->desc('too long description... omission')
            ->default('%a %b %e %T %Z %Y')
            ->string('FORMAT');

        $expected = <<<HELP
Usage:
\tphp date [FLAG]... [FORMAT]

FLAG:
\t-d [STRING], --date[=STRING]
\t\tdisplay time described by STRING, not 'now'

\t--debug
\t\tannotate the parsed date, and warn about questionable usage to stderr

\t-I [FMT], --iso-8601[=FMT]
\t\toutput date/time in ISO 8601 format. FMT='date' for date only (the default),
\t\t'hours', 'minutes', 'seconds', or 'ns' for date and time to the indicated
\t\tprecision. Example: 2006-08-14T02:34:56-06:00

ARG:
\t[FORMAT]
\t\ttoo long description... omission


HELP;

        $scriptName = 'date';
        $helpGenerator = new HelpGenerator($scriptName);
        $this->assertSame($expected, $helpGenerator->generate($appSpec));
    }
}
