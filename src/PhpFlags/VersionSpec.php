<?php


namespace PhpFlags;


use Twig\Environment;
use Twig\Extension\StringLoaderExtension;
use Twig\Loader\ArrayLoader;
use Twig\Node\Expression\CallExpression;

class VersionSpec
{
    /**
     * @var string
     */
    private $version;
    /**
     * @var string
     */
    private $format;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $short;

    public function __construct(string $version)
    {
        $this->version = $version;
        $this->name = 'version';
        $this->short = 'v';
        $this->format = 'version {{VERSION}}';
    }

    public function format(string $format)
    {
        $this->format = $format;
    }

    // TODO: これはここでやる仕事ではないので、別なクラスに任せるようにする
    public function genMessage():string
    {
        $twig = new Environment(new ArrayLoader(['version' => $this->format]));
        return $twig->render('version', ['VERSION' => $this->version]);
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