# PHP Flags

This library is a parser of command line arguments that can be used sensibly without looking at the documentation.

## Feature

* Explicit declarations.
* Simple notation.
* Support for required, optional and multiple values.
* Automatic generation of usage.

## Installation

todo write

## Usage

### Method chain declaration

example of ping command.  
If you want to see other examples, see the script under `examples/chain/`.

```php
<?php
use PhpFlags\Parser;
use PhpFlags\Spec\ApplicationSpec;

// example ping
$spec = new ApplicationSpec();
$count = $spec->flag('count')->short('c')->default(-1)
    ->desc('Number of times to send an ICMP request. The default of -1 sends an unlimited number of requests.')
    ->validRule(function($count) {
        return $count >= -1;
    })
    ->int('request count');
$timeout = $spec->flag('timeout')->short('t')->default(5)
    ->desc('Timeout seconds for ICMP requests.')
    ->validRule(function($timeout) {
        return $timeout >= 0;
    })
    ->int('request count');
$verbose = $spec->flag('verbose')->short('v')
    ->desc('verbose output.')
    ->bool();
$host = $spec->arg()
    ->desc('IP of the host for the ICMP request.')
    ->validRule(function($ip){
        return preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $ip);
    })
    ->string('host');
try {
    Parser::create($spec)->parse($argv);
} catch (PhpFlags\InvalidArgumentsException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
} catch (PhpFlags\InvalidSpecException $e) {
    echo $e->getMessage(), PHP_EOL;
    exit(1);
}
echo "  count: ", $count->get(), PHP_EOL;
echo "timeout: ", $timeout->get(), PHP_EOL;
echo "verbose: ", $verbose->get() ? 'true' : 'false', PHP_EOL;
echo "   host: ", $host->get(), PHP_EOL;
```

```bash
# If the option is not defined in the argument, the default value is taken.
$ php ping.php 127.0.0.1
  count: -1
timeout: 5
verbose: false
   host: 127.0.0.1

# If a flag is specified, the value is stored in the corresponding object.
$ php ping.php -c 3 -t 10 -v 127.0.0.1
or $ php ping.php -c=3 -t=10 -v 127.0.0.1
  count: 3
timeout: 10
verbose: true
   host: 127.0.0.1

# InvalidArgumentsException is thrown 
# if a validRule is violated or the type of the passed value does not match the specified type. 
$ php ping.php -t=foo 127.0.0.1
The values does not matched the specified type. expect_type:int, given_type:string, value:foo

$ php ping.php -t=-1 127.0.0.1
invalid by validRule. flag:--timeout, value:-1
```

Usage is automatically generated without any special configuration.

```bash
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
```

## Document

### Support Type

* int
* float
* bool
* string
* date

### Building flag and arg options

method name|description|
---|---
desc|Set description.
default|Set default value. If the default value is not specified, it is treated as a required flag (other bool).
validRule|Set a callback that throws an exception as an invalid value if false is returned. Expected callback format is f($value) { return boolean; }
multiple|Allow multiple option values. (e.g. If -f 1 -f 2 -f 3, get values [1, 2, 3])

### Building flag additional option

method name|description|
---|---
short|Enable short flag and set short flag name.
