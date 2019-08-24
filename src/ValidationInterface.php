<?php


namespace Jeekens\Validation;


interface ValidationInterface
{

    public static function init();

    public static function addRule(string $name, RuleInterface $rule);

    public static function getRules(): array;

    public static function make(array $data, array $rule, ?array $msg = null): ValidateInterface;

}