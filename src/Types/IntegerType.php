<?php declare(strict_types=1);


namespace Jeekens\Validation\Types;


use function is_int;

/**
 * Class IntegerType
 *
 * @package Jeekens\Validation\Types
 */
class IntegerType implements TypeRuleInterface
{

    public function check($value): bool
    {
        return is_int($value);
    }

    public function isEmpty($value): bool
    {
        return $value === 0;
    }

    public function getRuleMethod(string $rule): ?string
    {
        return null;
    }

    public function bindTyped(): ?string
    {
        return null;
    }

    public function getName(): string
    {
        return 'integer';
    }

    public function getMessage(): ?string
    {
        return '{:field} must be a integer.';
    }

    public function getSize($value): ?string
    {
        return (string) $value;
    }

}