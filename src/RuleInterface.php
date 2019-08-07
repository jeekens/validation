<?php


namespace Jeekens\Validation;


interface RuleInterface
{

    public function check($value): bool;

}