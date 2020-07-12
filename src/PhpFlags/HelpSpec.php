<?php


namespace PhpFlags;


use Twig\Environment;
use Twig\Loader\ArrayLoader;

class HelpSpec
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $short;

    public function __construct()
    {
        $this->name = 'help';
        $this->short = 'h';
    }

    public function getLong(): string
    {
        return '--' . $this->name;
    }

    public function getShort(): string
    {
        return '-' . $this->short;
    }
}