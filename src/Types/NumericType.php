<?php declare(strict_types=1);


namespace Jeekens\Validation\Types;


use function is_numeric;

/**
 * Class NumericType
 *
 * @package Jeekens\Validation\Types
 */
class NumericType implements TypeRuleInterface
{

    public function check($value): bool
    {
        return is_numeric($value);
    }

    public function isEmpty($value): bool
    {
        return bccomp((string)$value, '0', 5) === 0;
    }

    public function getSize($value): ?string
    {
        return (string) $value;
    }

    public function getRuleMethod(string $rule): ?string
    {
        return null;
    }

    public function getMessage(): ?string
    {
        return '{:field} must be a numeric';
    }

    public function getName(): string
    {
        return 'numeric';
    }

    public function bindTyped(): ?string
    {
        return null;
    }

}