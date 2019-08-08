<?php declare(strict_types=1);


namespace Jeekens\validation\Rule;


use Jeekens\Basics\Arr;
use Jeekens\validation\TypeRule;

class ArrayType extends TypeRule
{

    protected $format = [
        'index' => 'indexCheck',
        'assoc' => 'assocCheck',
    ];

    protected $ruleName = 'array';

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