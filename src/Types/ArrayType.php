<?php declare(strict_types=1);


namespace Jeekens\validation\Types;


use Jeekens\Basics\Arr;
use function count;
use function is_array;

/**
 * Class ArrayType
 *
 * @package Jeekens\validation\Types
 */
class ArrayType implements TypeRuleInterface
{

    protected $method = [
        'index' => 'checkIndex',
        'assoc' => 'checkAssoc',
    ];


    public function check($value): bool
    {
        return is_array($value);
    }

    public function isEmpty($value): bool
    {
        return $value === [];
    }

    public function getName(): string
    {
        return 'array';
    }

    public function getSize($value): ?string
    {
        return (string) count($value);
    }

    public function bindTyped(): ?string
    {
        return null;
    }

    public function getMessage(): ?string
    {
        return '{:field} must be an array.';
    }

    public function getRuleMethod(string $rule): ?string
    {
        return $this->method[$rule] ?? null;
    }

    public function checkIndex($value)
    {
        return Arr::isIndex($value);
    }

    public function checkAssoc($value)
    {
        return Arr::isAssoc($value);
    }

}