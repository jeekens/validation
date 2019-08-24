<?php declare(strict_types=1);


namespace Jeekens\validation\Types;


use function is_array;

class ArrayType implements TypeRuleInterface
{

    public function check($value): bool
    {
        return is_array($value);
    }

    public function isEmpty($value): bool
    {
        return $value === [];
    }

}