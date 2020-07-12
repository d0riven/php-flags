<?php

use PhpFlags\ApplicationSpec;

describe('ApplicationSpec', function () {
    beforeEach(function () {
        $this->spec = new ApplicationSpec();
    });

    describe('Flag', function () {
        describe('int', function () {
            beforeEach(function () {
                $spec = $this->spec; /** @var ApplicationSpec $spec */
                $this->val = $spec->flag('long')->default(3)->short('s')->int('v');
            });

            context('when exists long flag with value 1', function () {
                $argv = explode(' ', 'test.php --long 1');
                it('return int value 1', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('integer');
                    expect($this->val->get())->toBe(1);
                });
            });
            context('when exists short flag with value 2', function () {
                $argv = explode(' ', 'test.php -s 2');
                it('return int value 1', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('integer');
                    expect($this->val->get())->toBe(2);
                });
            });
            context('when no flag', function () {
                $argv = explode(' ', 'test.php');
                it('return default int value 3', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('integer');
                    expect($this->val->get())->toBe(3);
                });
            });
        });

        describe('float', function () {
            beforeEach(function () {
                $spec = $this->spec; /** @var ApplicationSpec $spec */
                $this->val = $spec->flag('long')->default(3.3)->short('s')->float('v');
            });

            context('when exists long flag with value 1.1', function () {
                $argv = explode(' ', 'test.php --long 1.1');
                it('return float value 1.1', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('float');
                    expect($this->val->get())->toBe(1.1);
                });
            });
            context('when exists short flag with value 2.2', function () {
                $argv = explode(' ', 'test.php -s 2.2');
                it('return float value 2.2', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('float');
                    expect($this->val->get())->toBe(2.2);
                });
            });
            context('when no flag', function () {
                $argv = explode(' ', 'test.php');
                it('return default float value 3.3', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('float');
                    expect($this->val->get())->toBe(3.3);
                });
            });
        });

        describe('string', function () {
            beforeEach(function () {
                $spec = $this->spec; /** @var ApplicationSpec $spec */
                $this->val = $spec->flag('long')->default('def')->short('s')->string('v');
            });

            context('when exists long flag with value long', function () {
                $argv = explode(' ', 'test.php --long long');
                it('return string long', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('string');
                    expect($this->val->get())->toBe('long');
                });
            });
            context('when exists short flag with value short', function () {
                $argv = explode(' ', 'test.php -s short');
                it('return string short', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('string');
                    expect($this->val->get())->toBe('short');
                });
            });
            context('when no flag', function () {
                $argv = explode(' ', 'test.php');
                it('return default string def', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('string');
                    expect($this->val->get())->toBe('def');
                });
            });
        });

        describe('date', function () {
            beforeEach(function () {
                $spec = $this->spec; /** @var ApplicationSpec $spec */
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

        describe('bool', function () {
            beforeEach(function () {
                $spec = $this->spec; /** @var ApplicationSpec $spec */
                $this->val = $spec->flag('long')->short('s')->bool();
            });

            context('when exists long flag', function () {
                $argv = explode(' ', 'test.php --long');
                it('return true', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('boolean');
                    expect($this->val->get())->toBe(true);
                });
            });
            context('when exists short flag', function () {
                $argv = explode(' ', 'test.php -s');
                it('return true', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('boolean');
                    expect($this->val->get())->toBe(true);
                });
            });
            context('when no flag', function () {
                $argv = explode(' ', 'test.php');
                it('return false', function () use ($argv) {
                    PhpFlags\Parser::create($this->spec)->parse($argv);
                    expect($this->val->get())->toBeA('boolean');
                    expect($this->val->get())->toBe(false);
                });
            });
        });

    });
});

