<?php

use PhpFlags\ApplicationSpec;
use PhpFlags\InvalidArgumentsException;

describe('feature parse based on the ApplicationSpec', function () {
    beforeEach(function () {
        $this->spec = new ApplicationSpec();
    });

    describe('scenario ping', function () {
        beforeEach(function () {
            $spec = $this->spec;
            /** @var ApplicationSpec $spec */
            $this->count = $spec->flag('count')->short('c')->default(-1)
                ->desc('Number of times to send an ICMP request. The default of -1 sends an unlimited number of requests.')
                ->int('request count');
            $this->timeout = $spec->flag('timeout')->short('t')->default(5)
                ->desc('Timeout seconds for ICMP requests.')
                ->int('request count');
            $this->verbose = $spec->flag('verbose')->short('V')
                ->desc('verbose output.')
                ->bool();
            $this->host = $spec->arg()
                ->desc('IP of the host for the ICMP request.')
                ->string('host');
        });

        context('when only arg host ip', function() {
            $argv = explode(' ', 'ping 127.0.0.1');
            it('count is default int -1, timeout is default 5, verbose is false, and host is 127.0.0.1', function () use ($argv) {
                PhpFlags\Parser::create($this->spec)->parse($argv);
                expect($this->count->get())->toBe(-1);
                expect($this->timeout->get())->toBe(5);
                expect($this->verbose->get())->toBe(false);
                expect($this->host->get())->toBe('127.0.0.1');
            });
        });

        context('when exists count short -c flag with value of 10', function() {
            $argv = explode(' ', 'ping -c 10 127.0.0.1');
            it('count is int 10, timeout is default 5, verbose is false, and host is 127.0.0.1', function () use ($argv) {
                PhpFlags\Parser::create($this->spec)->parse($argv);
                expect($this->count->get())->toBe(10);
                expect($this->timeout->get())->toBe(5);
                expect($this->verbose->get())->toBe(false);
                expect($this->host->get())->toBe('127.0.0.1');
            });
        });

        context('when exists count short -c flag with value of 10, and timeout long short -t flag with value 1', function() {
            $argv = explode(' ', 'ping -c 10 -t=1 127.0.0.1');
            it('return count int 10, timeout is 1, verbose is false, and host 127.0.0.1', function () use ($argv) {
                PhpFlags\Parser::create($this->spec)->parse($argv);
                expect($this->count->get())->toBe(10);
                expect($this->timeout->get())->toBe(1);
                expect($this->verbose->get())->toBe(false);
                expect($this->host->get())->toBe('127.0.0.1');
            });
        });

        context('when exists count short -c flag with value of 10, timeout long short -t flag with value 1, and -V flag', function() {
            $argv = explode(' ', 'ping -c 10 -t=1 -V 127.0.0.1');
            it('return count int 10, timeout is 1, verbose is true, and host 127.0.0.1', function () use ($argv) {
                PhpFlags\Parser::create($this->spec)->parse($argv);
                expect($this->count->get())->toBe(10);
                expect($this->timeout->get())->toBe(1);
                expect($this->verbose->get())->toBe(true);
                expect($this->host->get())->toBe('127.0.0.1');
            });
        });

        context('when exists count short -c flag with invalid value of twice', function() {
            $argv = explode(' ', 'ping -c twice 127.0.0.1');
            it('throw InvalidArgumentsException', function () use ($argv) {
                $closure = function() use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                };
                expect($closure)->toThrow(new InvalidArgumentsException(
                    'The values does not matched the specified type. expect_type:int, given_type:string, value:twice'
                ));
            });
        });
    });

    describe('Flag', function () {
        context('int', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->flag('long')->default(3)->short('s')->int('v');
            });

            context('when exists long flag with value 1', function () {
                $argv = explode(' ', 'test.php --long 1');
                it('return int value 1', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(1);
                });
            });
            context('when exists short flag with value 2', function () {
                $argv = explode(' ', 'test.php -s 2');
                it('return int value 1', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(2);
                });
            });
            context('when no flag', function () {
                $argv = explode(' ', 'test.php');
                it('return default int value 3', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(3);
                });
            });
        });

        context('multiple int', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->flag('long')->multiple()->default([1, 2, 3])->short('s')->int('v');
            });

            context('when exists long and short flag with value [1, 2]', function () {
                $argv = explode(' ', 'test.php --long 1 -s 2');
                it('return int values [1, 2]', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe([1, 2]);
                });
            });
            context('when no flag', function () {
                $argv = explode(' ', 'test.php');
                it('return default int values [1, 2, 3]', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe([1, 2, 3]);
                });
            });
        });

        context('float', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->flag('long')->default(3.3)->short('s')->float('v');
            });

            context('when exists long flag with value 1.1', function () {
                $argv = explode(' ', 'test.php --long 1.1');
                it('return float value 1.1', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(1.1);
                });
            });
            context('when exists short flag with value 2.2', function () {
                $argv = explode(' ', 'test.php -s 2.2');
                it('return float value 2.2', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(2.2);
                });
            });
            context('when no flag', function () {
                $argv = explode(' ', 'test.php');
                it('return default float value 3.3', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(3.3);
                });
            });
        });

        context('string', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->flag('long')->default('def')->short('s')->string('v');
            });

            context('when exists long flag with value long', function () {
                $argv = explode(' ', 'test.php --long long');
                it('return string long', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe('long');
                });
            });
            context('when exists short flag with value short', function () {
                $argv = explode(' ', 'test.php -s short');
                it('return string short', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe('short');
                });
            });
            context('when no flag', function () {
                $argv = explode(' ', 'test.php');
                it('return default string def', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe('def');
                });
            });
        });

        context('date', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->flag('long')->default(new DateTimeImmutable('2000-03-01 00:00:00'))->short('s')->date('v');
            });

            context('when exists long flag with value 2000-01-01', function () {
                $argv = explode(' ', 'test.php --long 2000-01-01');
                it('return DateTimeImmutable 2000-01-01', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeAnInstanceOf('DateTimeImmutable');
                    expect($this->val->get()->format(DATE_ATOM))->toBe('2000-01-01T00:00:00+00:00');
                });
            });
            context('when exists short flag with value 2000-02-01', function () {
                $argv = explode(' ', 'test.php -s 2000-02-01');
                it('return DateTimeImmutable 2000-02-01', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeAnInstanceOf('DateTimeImmutable');
                    expect($this->val->get()->format(DATE_ATOM))->toBe('2000-02-01T00:00:00+00:00');
                });
            });
            context('when no flag', function () {
                $argv = explode(' ', 'test.php');
                it('return default DateTimeImmutable 2000-03-01', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeAnInstanceOf('DateTimeImmutable');
                    expect($this->val->get()->format(DATE_ATOM))->toBe('2000-03-01T00:00:00+00:00');
                });
            });
        });

        context('bool', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->flag('long')->short('s')->bool();
            });

            context('when exists long flag', function () {
                $argv = explode(' ', 'test.php --long');
                it('return true', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(true);
                });
            });
            context('when exists short flag', function () {
                $argv = explode(' ', 'test.php -s');
                it('return true', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(true);
                });
            });
            context('when no flag', function () {
                $argv = explode(' ', 'test.php');
                it('return false', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(false);
                });
            });
        });
    });


    describe('Arg', function () {
        context('int', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->arg()->default(3)->int('v');
            });

            context('when exists arg with value 1', function () {
                $argv = explode(' ', 'test.php 1');
                it('return int 1', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(1);
                });
            });
            context('when no arg', function () {
                $argv = explode(' ', 'test.php');
                it('return default int 3', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(3);
                });
            });
        });

        context('multiple int', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->arg()->multiple()->default([1, 2, 3])->int('v');
            });

            context('when exists arg with single value 1', function () {
                $argv = explode(' ', 'test.php 1');
                it('return int values [1]', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe([1]);
                });
            });
            context('when exists arg with value [1, 2]', function () {
                $argv = explode(' ', 'test.php 1 2');
                it('return int values [1, 2]', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe([1, 2]);
                });
            });
            context('when no arg', function () {
                $argv = explode(' ', 'test.php');
                it('return default int values [1, 2, 3]', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe([1, 2, 3]);
                });
            });
        });

        context('float', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->arg()->default(3.3)->float('v');
            });

            context('when exists arg with value 1.1', function () {
                $argv = explode(' ', 'test.php 1.1');
                it('return float 1.1', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(1.1);
                });
            });
            context('when no arg', function () {
                $argv = explode(' ', 'test.php');
                it('return default float 3.3', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(3.3);
                });
            });
        });

        context('string', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->arg()->default('def')->string('v');
            });

            context('when exists arg with value str', function () {
                $argv = explode(' ', 'test.php str');
                it('return string "str"', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe('str');
                });
            });
            context('when no arg', function () {
                $argv = explode(' ', 'test.php');
                it('return default string "def"', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe('def');
                });
            });
        });

        context('date', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->arg()->default(new DateTimeImmutable('2020-02-01'))->date('v');
            });

            context('when exists arg with value 2000-01-01', function () {
                $argv = explode(' ', 'test.php 2020-01-01');
                it('return string "str"', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeAnInstanceOf('DateTimeImmutable');
                    expect($this->val->get()->format(DATE_ATOM))->toBe('2020-01-01T00:00:00+00:00');
                });
            });
            context('when no arg', function () {
                $argv = explode(' ', 'test.php');
                it('return default string "def"', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeAnInstanceOf('DateTimeImmutable');
                    expect($this->val->get()->format(DATE_ATOM))->toBe('2020-02-01T00:00:00+00:00');
                });
            });
        });

        context('bool', function () {
            beforeEach(function () {
                $spec = $this->spec;
                /** @var ApplicationSpec $spec */
                $this->val = $spec->arg()->default(true)->bool();
            });

            context('when exists arg with value true', function () {
                $argv = explode(' ', 'test.php true');
                it('return bool true', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(true);
                });
            });
            context('when exists arg with value false', function () {
                $argv = explode(' ', 'test.php false');
                it('return bool false', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(false);
                });
            });
            context('when no arg', function () {
                $argv = explode(' ', 'test.php');
                it('return default bool true', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBe(true);
                });
            });
        });
    });
});

