<?php


namespace Jeekens\Validation;


class Validator
{

    protected $typeRules = [];

    protected $sizeRules = ['size', 'between', 'min', 'max', 'gt', 'lt', 'eq', 'neq'];

    protected $compareRules = ['in', 'notIn'];

    protected $implicitRules = ['required', 'requiredWith', 'requiredWithout', 'requiredIf', 'requiredUnless'];

}