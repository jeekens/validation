<?php


namespace Jeekens\Validator\Rule;


use Jeekens\Basics\Arr;
use Jeekens\Validator\TypeRule;

class ArrayType extends TypeRule
{

    protected $format = [
        'index' => 'indexCheck',
        'assoc' => 'assocCheck',
    ];

    public function check($value): bool
    {
        return is_array($value);
    }

    public function getSize($value, ?string $format): int
    {
        return count($value);
    }

    public function getDefaultFormat(): ?string
    {
        return null;
    }

    public function indexCheck(array $value): bool
    {
        return !Arr::isAssoc($value);
    }

    public function assocCheck(array $value): bool
    {
        return Arr::isAssoc($value);
    }

}