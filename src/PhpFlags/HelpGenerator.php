<?php


namespace PhpFlags;


use PhpFlags\Spec\ApplicationSpec;
use PhpFlags\Spec\ArgSpec;
use PhpFlags\Spec\FlagSpec;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class HelpGenerator
{
    /**
     * @var string
     */
    private $scriptName;

    /**
     * @var int
     */
    private $screenWidth = 78;

    public function __construct(string $scriptName)
    {
        $this->scriptName = $scriptName;
    }

    public function generate(ApplicationSpec $appSpec): string
    {
        $format = <<<FORMAT
Usage:
{{ usageClause }}

FLAG:
{% for flagClause in flagClauses %}
{{ flagClause | raw }}
{% endfor %}

ARG:
{% for argClause in argClauses %}
{{ argClause }}
{% endfor %}
FORMAT;

        $twig = new Environment(new ArrayLoader(['help' => $format]));

        return $twig->render('help', [
            'usageClause' => $this->generateUsage($appSpec),
            'flagClauses' => $this->generateFlags($appSpec),
            'argClauses' => $this->generateArgs($appSpec),
        ]);
    }

    private function generateUsage(ApplicationSpec $appSpec)
    {
        $requiredFlags = [];
        // 必須オプションは将来的には廃止する方向で行きたい
        /** @var FlagSpec $flagSpec */
        foreach ($appSpec->getFlagSpecCollection() as $flagSpec) {
            if (!$flagSpec->isRequired()) {
                continue;
            }
            $valueName = $flagSpec->getValue()->name();

            $flags = [];
            if ($flagSpec->hasShort()) {
                $flags[] = $flagSpec->getShort();
            }
            $flags[] = $flagSpec->getLong();

            $requiredFlags[] = (Type::BOOL()->equals($flagSpec->getType())) ?
                sprintf("%s", implode(", ", $flags)) :
                sprintf("%s=%s", implode(", ", $flags), $valueName);
        }

        $args = [];
        /** @var ArgSpec $argSpec */
        foreach ($appSpec->getArgSpecCollection() as $argSpec) {
            $arg = $argSpec->isRequired() ?
                sprintf("(%s)", $argSpec->getName())
                : sprintf("[%s]", $argSpec->getName());
            if ($argSpec->allowMultiple()) {
                $arg .= '...';
            }
            $args[] = $arg;
        }

        // TODO: 必須フラグはgetoptが招いた悪しき慣習なのでサポートしつつもdeprecated扱いにする
        return count($requiredFlags) > 0 ?
            sprintf("\tphp %s %s [FLAG]... %s", $this->scriptName, implode(' ', $requiredFlags), implode(' ', $args)) :
            sprintf("\tphp %s [FLAG]... %s", $this->scriptName, implode(' ', $args));
    }

    /**
     * @param ApplicationSpec $appSpec
     *
     * @return string[]
     */
    private function generateFlags(ApplicationSpec $appSpec): array
    {
        $flagClauses = [];
        foreach ($appSpec->getFlagSpecCollection() as $flagSpec) {
            $valueName = $flagSpec->getValue()->name();

            $flags = [];
            if ($flagSpec->hasShort()) {
                $flags[] = (Type::BOOL()->equals($flagSpec->getType())) ?
                    sprintf("%s", $flagSpec->getShort()) :
                    sprintf("%s %s", $flagSpec->getShort(), $flagSpec->isRequired() ? $valueName : "[${valueName}]");
            }

            if (Type::BOOL()->equals($flagSpec->getType())) {
                $flags[] = sprintf("%s", $flagSpec->getLong());
            } else {
                $flags[] = ($flagSpec->isRequired()) ?
                    sprintf("%s=%s", $flagSpec->getLong(), $valueName) :
                    sprintf("%s[=%s]", $flagSpec->getLong(), $valueName);
            }

            $designator = sprintf("\t%s", implode(", ", $flags));

            $flagClauses[] = $flagSpec->hasDescription() ?
                sprintf("%s%s\n", $designator,
                    wordwrap("\n\t\t" . $flagSpec->getDescription(), $this->screenWidth, "\n\t\t")) :
                sprintf("%s\n", $designator);
        }

        // trim last flag help message newline
        $flagClauses[count($flagClauses) - 1] = rtrim($flagClauses[count($flagClauses) - 1], "\n");

        return $flagClauses;
    }

    private function generateArgs(ApplicationSpec $appSpec): array
    {
        $argClauses = [];
        foreach ($appSpec->getArgSpecCollection() as $argSpec) {
            $valueName = $argSpec->hasDefault() ?
                sprintf("[%s]", $argSpec->getValue()->name()) : $argSpec->getValue()->name();

            $argClauses[] = $argSpec->hasDescription() ?
                sprintf("\t%s%s\n", $valueName,
                    wordwrap("\n\t\t" . $argSpec->getDescription(), $this->screenWidth, "\n\t\t")) :
                sprintf("\t%s\n", $valueName);
        }

        return $argClauses;
    }
}