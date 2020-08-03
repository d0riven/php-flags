<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpFlags\Parser;
use PhpFlags\Spec\ApplicationSpec;

// example ping
$spec = ApplicationSpec::create();
$spec->version('1.0.0')->clearShort();
$count = $spec->flag('count')->short('c')->default(-1)
    ->desc('Number of times to send an ICMP request. The default of -1 sends an unlimited number of requests.')
    ->validRule(function ($count) {
        return $count >= -1;
    })
    ->int('request count');
$timeout = $spec->flag('timeout')->short('t')->default(5)
    ->desc('Timeout seconds for ICMP requests.')
    ->validRule(function ($timeout) {
        return $timeout >= 0;
    })
    ->int('request count');
$verbose = $spec->flag('verbose')->short('v')
    ->desc('verbose output.')
    ->bool();
$host = $spec->arg()
    ->desc('IP of the host for the ICMP request.')
    ->validRule(function ($ip) {
        return preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $ip);
    })
    ->string('host');
/*
$ php ping.php --help
or $ php ping.php -h
Usage:
        php ping.php [FLAG]... (host)

FLAG:
        -c [request count], --count[=request count]
                Number of times to send an ICMP request. The default of -1 sends an unlimited
                number of requests.

        -t [request count], --timeout[=request count]
                Timeout seconds for ICMP requests.

        -v, --verbose
                verbose output.

ARG:
        host
                IP of the host for the ICMP request.
*/
try {
    Parser::create($spec)->parse($argv);
} catch (PhpFlags\InvalidArgumentsException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
} catch (PhpFlags\InvalidSpecException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
}

/*
$ php ping.php 127.0.0.1
  count: -1
timeout: 5
verbose: false
   host: 127.0.0.1

$ php ping.php -c 3 -t 10 -v 127.0.0.1
or $ php ping.php -c=3 -t=10 -v 127.0.0.1
  count: 3
timeout: 10
verbose: true
   host: 127.0.0.1
*/
echo "  count: ", $count->get(), PHP_EOL;
echo "timeout: ", $timeout->get(), PHP_EOL;
echo "verbose: ", $verbose->get() ? 'true' : 'false', PHP_EOL;
echo "   host: ", $host->get(), PHP_EOL;
