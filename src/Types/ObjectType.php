<?php declare(strict_types=1);


namespace Jeekens\Validation\Types;


use function is_object;

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

}