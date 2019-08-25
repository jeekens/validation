<?php


namespace Jeekens\Validation;


interface RuleInterface
{

    public function check($value): bool;

    public function getName(): string;

    public function bindTyped(): ?string;

    public function getMessage(string $rule): ?string;

}