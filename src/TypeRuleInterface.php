<?php


namespace Jeekens\Validation;


interface TypeRuleInterface extends RuleInterface
{

    public function compare($value, string $condition, array $attribute): bool;

    public function getSize($value, ?string $format): int;

    public function formatCheck($value, ?string $format): bool;

    public function getDefaultFormat(): ?string;

}