<?php declare(strict_types=1);


namespace Jeekens\Validation\Types;


use function bccomp;
use function is_float;

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

}