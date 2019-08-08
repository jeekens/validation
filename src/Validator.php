<?php declare(strict_types=1);


namespace Jeekens\Validation;

use Jeekens\Basics\Arr;
use Jeekens\validation\Rule\DateType;
use Jeekens\validation\Rule\ArrayType;
use Jeekens\validation\Rule\StringType;

class Validator
{

    protected $typeRules = [];

    protected $rules = [
        'required' => 'vaRequired',
        'requiredIf' => 'vaRequiredIf',
        'requiredWith' => 'vaRequiredWith',
        'requiredWithAll' => 'vaRequiredWithAll',
        'requiredWithOut' => 'vaRequiredWithOut',
        'requiredWithOutAll' => 'vaRequiredWithOutAll',
        'requiredUnless' => 'vaRequiredUnless',
        'size' => 'vaSize',
        'between' => 'vaBetween',
        'min' => 'vaMin',
        'max' => 'vaMax',
        'gt' => 'vaGt',
        'lt' => 'vaLt',
        'eq' => 'vaEq',
        'neq' => 'vaNeq',
        'leq' => 'vaLeq',
        'geq' => 'vaGeq',
        'in' => 'vaIn',
        'notIn' => 'vaNotIn',
        'confirm' => 'vaConfirm',
        'confirmNot' => 'vaConfirmNot',
        'emptyNot' => 'vaEmptyNot',
    ];

    protected $propertyResult = [];

    protected $dataType = [];

    protected $data = [];

    /**
     * @var string|null
     */
    protected $nowEachIndex = null;

    /**
     * @var Validation
     */
    protected $validation;

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
                $nowRule = $item;
                if (stripos($item, ':')) {
                    $tmp = explode(':', $item, 2);
                    $nowRule = $tmp[0];
                    $param = explode(',', $tmp[1]);
                    $rule[$key] = [$nowRule, $param];
                }

                /**
                 * @var $typeRule TypeRuleInterface
                 */
                if (($typeRule = $this->typeRules[$nowRule] ?? null)) {

                    if (is_array($rule[$key])) {
                        $this->dataType[$attributeName] = [$nowRule, $param];
                    } else {
                        $this->dataType[$attributeName] = [$nowRule, [$typeRule->getDefaultFormat()]];
                    }

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
        $this->setValue($data);
        $validation = new Validation();

        foreach ($rules as $key => $rule) {

            if (isset($this->propertyResult[$key])) {
                continue;
            }

            $this->propertyResult[$key] = $this->validationProperty($key, $rule, $validation);
        }


    }

    protected function validationProperty($key, $rules, $validation): bool
    {
        $dataType = $this->dataType[$key] ?? null;

        if ($dataType === null) {
            $dataType = gettype($this->getValue($key));
        }

    }

    protected function getValue(string $key, bool $is_each = false)
    {
        return Arr::get($this->data, $key);
    }

    protected function setValue($data)
    {
        $this->data = $data;
    }

}