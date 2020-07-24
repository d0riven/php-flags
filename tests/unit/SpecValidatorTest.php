<?php


use PhpFlags\Spec\ApplicationSpec;
use PhpFlags\Spec\ArgSpecCollection;
use PhpFlags\Spec\HelpSpec;
use PhpFlags\Spec\VersionSpec;
use PhpFlags\SpecValidator;
use PHPUnit\Framework\TestCase;

class SpecValidatorTest extends TestCase
{

    /**
     * @test
     * @dataProvider validationFlagsDataProvider
     */
    public function validationFlags(array $flagSpecs, array $expectedInvalidReasons)
    {
        $helpSpec = new HelpSpec();
        $versionSpec = new VersionSpec('1.0');
        $this->assertSame($expectedInvalidReasons, SpecValidator::validationFlags($flagSpecs, $helpSpec, $versionSpec));
    }

    public function validationFlagsDataProvider()
    {
        return [
            'multiple bool is not supported' => [
                'flagSpecs' => (function () {
                    $appSpec = new ApplicationSpec();
                    $appSpec->flag('long')->multiple()->bool();

                    return $appSpec->getFlagSpecs();
                })(),
                'expected' => [
                    'bool type is not supported multiple. flag:--long',
                ],
            ],
            'duplicate flag name case of "h" short flag (duplicate help short flag)' => [
                'flagSpecs' => (function () {
                    $appSpec = new ApplicationSpec();
                    $appSpec->flag('height')->short('h')->int('height');

                    return $appSpec->getFlagSpecs();
                })(),
                'expected' => [
                    'duplicate flag name. name:-h, duplicate_count:2',
                ],
            ],
            'duplicate flag name case of "v" short flag (duplicate version short flag)' => [
                'flagSpecs' => (function () {
                    $appSpec = new ApplicationSpec();
                    $appSpec->flag('verbose')->short('v')->bool();

                    return $appSpec->getFlagSpecs();
                })(),
                'expected' => [
                    'duplicate flag name. name:-v, duplicate_count:2',
                ],
            ],
            'duplicate flag name case of "long" flag' => [
                'flagSpecs' => (function () {
                    $appSpec = new ApplicationSpec();
                    $appSpec->flag('long')->bool();
                    $appSpec->flag('long')->int('long');

                    return $appSpec->getFlagSpecs();
                })(),
                'expected' => [
                    'duplicate flag name. name:--long, duplicate_count:2',
                ],
            ],
        ];
    }

    /**
     * @test
     * @dataProvider validationArgsDataProvider
     */
    public function validationArgs(ArgSpecCollection $argSpecCollection, array $expectedInvalidReasons)
    {
        $this->assertSame($expectedInvalidReasons, SpecValidator::validationArgs($argSpecCollection));
    }

    public function validationArgsDataProvider()
    {
        return [
            'multiple options are specified that are not the last argument' => [
                'argSpecs' => (function () {
                    $appSpec = new ApplicationSpec();
                    $appSpec->arg()->multiple()->int('ints');
                    $appSpec->arg()->string('string');

                    return $appSpec->getArgSpecCollection();
                })(),
                'expected' => [
                    'multiple value option are only allowed for the last argument',
                ],
            ],
            'arguments are a mixture of required and optional' => [
                'argSpecs' => (function () {
                    $appSpec = new ApplicationSpec();
                    $appSpec->arg()->int('required value');
                    $appSpec->arg()->default(3)->int('optional value');

                    return $appSpec->getArgSpecCollection();
                })(),
                'expected' => [
                    'args should be all of required or optional (cannot mix required and optional args)',
                ],
            ],
        ];
    }
}
