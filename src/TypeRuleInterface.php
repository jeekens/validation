<?php


namespace Jeekens\Validation;


interface TypeRuleInterface extends RuleInterface
{

    public function compare($value, string $condition, array $attribute): bool;

    public function getTypeName(): ?string;

    public function getRelyTypeName(): string;

    public function count($value): int;

}