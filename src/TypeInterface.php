<?php


namespace Jeekens\Validation;


use Jeekens\Validation\Rule\RuleInterface;

interface TypeInterface extends RuleInterface
{

    public function checkType($value);

}