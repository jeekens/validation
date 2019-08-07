<?php


namespace Jeekens\Validator\Rule;


use Jeekens\Basics\Str;
use Jeekens\Validation\TypeRuleInterface;

class StringType implements TypeRuleInterface
{

    public function check($value): bool
    {
        // TODO: Implement check() method.
    }


    public function compare($condition, $value): bool
    {
        // TODO: Implement compare() method.
    }

    public function getTypeName(): string
    {
        return 'string';
    }

    public function count($value): int
    {
        return Str::length($value);
    }

}