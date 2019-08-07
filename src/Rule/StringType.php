<?php


namespace Jeekens\Validator\Rule;


use Jeekens\Basics\Str;
use Jeekens\Validator\TypeRule;

class StringType extends TypeRule
{

    protected $typeName = 'string';

    protected $relyTypeName = 'string';


    public function check($value): bool
    {
        return is_string($value);
    }

    public function count($value): int
    {
        return Str::length($value);
    }

}