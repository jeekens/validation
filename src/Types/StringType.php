<?php declare(strict_types=1);


namespace Jeekens\validation\Types;


use Jeekens\Basics\Str;
use function is_string;
use function strlen;

class StringType implements TypeRuleInterface
{

    public function check($value): bool
    {
        return is_string($value);
    }

    public function isEmpty($value): bool
    {
        return $value === '';
    }

    public function getMessage(): ?string
    {
        return '{:field} must be a string';
    }

    public function getSize($value): ?string
    {
       return (string) Str::length($value);
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
        return 'string';
    }

}