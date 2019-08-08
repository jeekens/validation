<?php


namespace Jeekens\Validation;


interface RuleInterface
{

    public function check($value): bool;

    public function getRuleName(): string;

}