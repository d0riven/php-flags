<?php


namespace PhpFlags;


use DateTime;
use DateTimeImmutable;
use LogicException;
use MyCLabs\Enum\Enum;

/**
 * @method static Type INT()
 * @method static Type FLOAT()
 * @method static Type BOOL()
 * @method static Type STRING()
 * @method static Type DATE()
 */
class Type extends Enum
{
    private const INT = 'int';
    private const FLOAT = 'float';
    private const BOOL = 'bool';
    private const STRING = 'string';
    private const DATE = 'date';

    /**
     * @param mixed $value
     *
     * @return DateTimeImmutable|float|int|string
     *
     * @throws InvalidArgumentsException|LogicException
     */
    public function getTypedValue($value)
    {
        if (is_string($value)) {
            if (!$this->isValidStringValue($value)) {
                // TODO: write expect type, and given type
                throw new InvalidArgumentsException(sprintf(
                    'The value does not matched the specified type. type:%s, value:%s'
                    , $this->getValue(), $value
                ));
            }
            switch ($this->getValue()) {
                case self::INT:
                    return (int)$value;
                case self::FLOAT:
                    return (float)$value;
                case self::BOOL:
                    return strtolower($value) === 'true';
                case self::STRING:
                    return $value;
                case self::DATE:
                    return new DateTimeImmutable($value);
            }
            throw new LogicException('implements error. access to php-flags oss developer');
        }

        // set by defaults
        // multiple
        if (is_array($value)) {
            foreach ($value as $v) {
                if (!$this->isValidMixedValue($v)) {
                    // TODO: write expect type, and given type
                    throw new InvalidArgumentsException(sprintf(
                        'The default values does not matched the specified type. expect_type:%s, give_type:%s, value:%s, values:[%s]'
                        , $this->getValue(), gettype($v), $v, implode(',', $value)
                    ));
                }
            }

            return $value;
        }

        if (!$this->isValidMixedValue($value)) {
            // TODO: write expect type, and given type
            throw new InvalidArgumentsException(sprintf(
                'The default value does not matched the specified type. type:%s, value:%s'
                , $this->getValue(), $value
            ));
        }

        return $value;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function isValidStringValue(string $value): bool
    {
        $isValid = true;
        switch ($this->getValue()) {
            case self::INT:
                $isValid = $this->isIntegerString($value);
                break;
            case self::FLOAT:
                $isValid = $this->isFloatString($value);
                break;
            case self::BOOL:
                $isValid = $this->isBoolString($value);
                break;
            case self::STRING:
                $isValid = $this->isString($value);
                break;
            case self::DATE:
                $isValid = $this->isDateString($value);
                break;
        }

        return $isValid;
    }

    private function isIntegerString(string $value): bool
    {
        if (!is_numeric($value)) {
            return false;
        }

        return preg_match('/^[0-9]+$/', $value) === 1;
    }

    private function isFloatString(string $value): bool
    {
        return is_numeric($value);
    }

    private function isBoolString(string $value): bool
    {
        return strtolower($value) === 'true' || strtolower($value) === 'false';
    }

    private function isString(string $value): bool
    {
        return !is_numeric($value);
    }

    private function isDateString(string $value): bool
    {
        try {
            new DateTimeImmutable($value);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function isValidMixedValue($value): bool
    {
        $isValid = true;
        switch ($this->getValue()) {
            case self::INT:
                $isValid = is_int($value);
                break;
            case self::FLOAT:
                $isValid = is_float($value);
                break;
            case self::BOOL:
                // bool has not value. always true
                break;
            case self::STRING:
                $isValid = is_string($value);
                break;
            case self::DATE:
                $isValid = ($value instanceof DateTimeImmutable) || ($value instanceof DateTime);
                break;
        }

        return $isValid;
    }
}