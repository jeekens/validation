<?php


namespace Jeekens\Validation;


class Validator
{

    protected $typeValidator = [];

    protected $typeRules = [];

    protected $sizeRules = ['size', 'between', 'min', 'max', 'gt', 'lt', 'eq', 'neq', 'lqe', 'geq'];

    protected $compareRules = ['in', 'notIn'];

    protected $implicitRules = ['required', 'requiredWith', 'requiredWithout', 'requiredIf', 'requiredUnless', 'confirm', 'confirmNot'];

    protected $emptyRules = ['emptyNot'];

}