<?php


namespace Jeekens\Validator;


interface RuleInterface
{

    public function setContext($context = null);

    public function check($value): bool;

}