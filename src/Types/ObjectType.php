<?php declare(strict_types=1);


namespace Jeekens\Validation\Types;


use function is_object;

/**
 * Class ObjectType
 *
 * @package Jeekens\Validation\Types
 */
class ObjectType implements TypeRuleInterface
{

    public function check($value): bool
    {
        return is_object($value);
    }

    public function isEmpty($value): bool
    {
        return false;
    }

    public function getMessage(): ?string
    {
        return '{:field} must be an object.';
    }

    public function getSize($value): ?string
    {
        return null;
    }

    public function getRuleMethod(string $rule): ?string
    {
        return null;
    }

    public function getName(): string
    {
        return 'object';
    }

    public function bindTyped(): ?string
    {
        return null;
    }

}