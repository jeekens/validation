<?php declare(strict_types=1);


namespace Jeekens\validation\Types;


use function is_string;

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

}