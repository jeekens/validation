<?php declare(strict_types=1);


namespace Jeekens\Validation\Types;


use function bccomp;
use function is_float;

/**
 * Class FloatType
 * @package Jeekens\Validation\Types
 */
class FloatType implements TypeRuleInterface
{



    public function check($value): bool
    {
        return is_float($value);
    }


    public function isEmpty($value): bool
    {
        return bccomp((string)$value, '0', 5) === 0;
    }


    public function getSize($value): ?string
    {
        return (string) $value;
    }


    public function bindTyped(): ?string
    {
        return null;
    }


    public function getName(): string
    {
        return 'float';
    }


    public function getMessage(): ?string
    {
        return '{:field} must be an float.';
    }


    public function getRuleMethod(string $rule): ?string
    {
        return null;
    }

}