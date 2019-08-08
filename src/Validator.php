<?php declare(strict_types=1);


namespace Jeekens\Validation;

use Jeekens\validation\Rule\DateType;
use Jeekens\validation\Rule\ArrayType;
use Jeekens\validation\Rule\StringType;

class Validator
{

    protected $typeRules = [];

    protected $sizeRules = ['size', 'between', 'min', 'max', 'gt', 'lt', 'eq', 'neq', 'lqe', 'geq'];

    protected $compareRules = ['in', 'notIn'];

    protected $implicitRules = [
        'required',
        'requiredWith',
        'requiredWithout',
        'requiredWithoutAll',
        'requiredWithAll',
        'requiredUnless',
    ];

    protected $confirmRule = ['confirm', 'confirmNot'];

    protected $emptyRules = ['emptyNot'];

    protected $propertyResult = [];

    /**
     * Validator constructor.
     */
    public function __construct()
    {
        $this->addRule(new StringType());
        $this->addRule(new ArrayType());
        $this->addRule(new DateType());
    }

    /**
     * @param RuleInterface $rule
     *
     * @return $this
     */
    public function addRule(RuleInterface $rule)
    {
        $this->typeRules[$rule->getRuleName()] = $rule;

        return $this;
    }

    /**
     * @param $rules
     *
     * @return array
     */
    protected function parseRules($rules)
    {

        foreach ($rules as $attributeName => $rule) {
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }

            foreach ($rule as $key => $item) {
                if (stripos($item, ':')) {
                    $tmp = explode(':', $item, 2);
                    $param = explode(',', $tmp[1]);
                    $rule[$key] = [$tmp[0], $param];
                }
            }

            $rules[$attributeName] = $rule;
        }

        return $rules;
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array|null $messages
     *
     * @return Validation
     */
    public function validation(array $data, array $rules, ?array $messages = null): Validation
    {
        $rules = $this->parseRules($rules);

        foreach ($rules as $key => $rule) {

            if (isset($this->propertyResult[$key])) {
                continue;
            }

            $this->propertyResult[$key] = $this->validationProperty($data[$key] ?? null, $rule);
        }
    }

    protected function validationProperty($value, $rules): bool
    {
        
    }

}