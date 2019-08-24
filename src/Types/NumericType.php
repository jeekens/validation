<?php declare(strict_types=1);


namespace Jeekens\Validation\Types;


use function is_numeric;

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

}