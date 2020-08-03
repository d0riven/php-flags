<?php


namespace PhpFlags\Spec;


interface ApplicationSpecInterface
{
    public function flag(string $long): FlagSpecInterface;

    public function arg(): ArgSpecInterface;

    public function help(): HelpSpecInterface;

    public function version(string $version): VersionSpecInterface;
}