<?php declare(strict_types=1);


namespace Jeekens\Validation\Types;


use function is_int;

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

}