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
$spec->version('1.0.0')->clearShort();
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

Since version is defined, you can check the version with --version flag.

```bash
$ php ping.php --version
version 1.0.0
```

## Document

### Support Type

* int
* float
* bool
* string
* date

### Build flag and arg options

method name|description
---|---
desc|Set description.
default|Set default value. If the default value is not specified, it is treated as a required flag (other bool).
validRule|Set a callback that throws an exception as an invalid value if false is returned. Expected callback format is f($value) { return boolean; }
multiple|Allow multiple option values. (e.g. If -f 1 -f 2 -f 3, get values [1, 2, 3])

### Build flag additional option

method name|description
---|---
short|Enable short flag and set short flag name.

### Help flag additional option

method name|description
---|---
long|Replace long flag name from "help".
short|Replace short flag name from "h".
clearShort|Disable short flag.

### Version flag additional option

method name|description|
---|---
long|Replace long flag name from "version".
short|Replace short flag name from "v".
clearShort|Disable short flag.

### Custom help

Help is defined by default and the flags consist of --help and -h.  
If these flags are specified on the command line, it will normally output a formatted help message and exit at parse time with status 0.  
It is possible to change the flags and customize the behavior when they are specified.

If you want to see more info, see the script under `examples/chain/customVersionHelp.php`.

```php
<?php

use PhpFlags\Spec\ApplicationSpec;

$appSpec = new ApplicationSpec();
$appSpec->help()->long('show-help')->short('s')
    ->action(function ($helpMessage) {
        fputs(STDERR, $helpMessage);
        exit(1);
    });
$appSpec->flag('example-flag')->desc('example flag declaration')->int();
$appSpec->arg()->desc('example arg declaration')->int('EXAMPLE-ARG');
// ...
```

Changed the flag value from "help" to "show-help", short from "h" to "s", and the exit status code from "0" to "1".

```bash
   $ php examples/chain/customVersionHelp.php --show-help; echo $?
or $ php examples/chain/customVersionHelp.php -s; echo $?
Usage:
        php examples/chain/customVersionHelp.php --example-flag=int [FLAG]... (EXAMPLE-ARG)

FLAG:
        --example-flag=int
                example flag declaration

ARG:
        EXAMPLE-ARG
                example arg declaration

1 # return exit 1 status code
```

### Custom version

Changed the flag value from "version" to "ver", short from "v" to upper "V", and the exit status code from "0" to "1".

```php
<?php

use PhpFlags\Spec\ApplicationSpec;

$appSpec = new ApplicationSpec();
$appSpec->version('1.0')->long('ver')->short('V')
    ->format('app version: {{VERSION}}')
    ->action(function ($versionMessage) {
        fputs(STDERR, $versionMessage);
        exit(1);
    });
// ...
```

```bash
   $ php examples/chain/customVersionHelp.php --ver; echo $?
or $ php examples/chain/customVersionHelp.php -V; echo $?
app version: 1.0
1 # return exit 1 status code
```
