<?php


namespace Jeekens\Validation;


interface TypeRuleInterface extends RuleInterface
{

    public function compare($condition, $value): bool;

    public function getTypeName(): string;

    public function count($value): int;

}