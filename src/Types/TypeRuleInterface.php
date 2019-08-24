<?php


namespace Jeekens\Validation\Types;


use Jeekens\Validation\RuleInterface;

interface TypeRuleInterface extends RuleInterface
{

    public function isEmpty($value): bool;

    public function getSize($value): int;

}