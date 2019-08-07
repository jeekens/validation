<?php


namespace Jeekens\Validator;


use Jeekens\Validation\TypeRuleInterface;

abstract class TypeRule implements TypeRuleInterface
{

    protected $typeName = null;

    protected $relyTypeName = null;


    public function compare($value, string $condition, array $attribute): bool
    {
        return $this->$condition($value, $attribute);
    }

    protected function in($value, $array)
    {
        return array_search($value, $array) !== false;
    }

    protected  function notIn($value, $array)
    {
        return array_search($value, $array) === false;
    }

    public function getRelyTypeName(): string
    {
        return $this->relyTypeName;
    }

    public function getTypeName(): ?string
    {
        return $this->typeName;
    }

}