<?php


namespace Jeekens\Validator\Rule;


use Jeekens\Validator\TypeRule;

class ArrayType extends TypeRule
{

    protected $typeName = 'array';

    protected $relyTypeName = 'array';


    public function check($value): bool
    {
        return is_array($value);
    }

    public function count($value): int
    {
        return count($value);
    }

}