<?php declare(strict_types=1);


namespace Jeekens\Validation;


use InvalidArgumentException;
use Jeekens\Basics\Arr;
use Jeekens\Validation\Exception\InvalidRuleException;
use Jeekens\validation\Types\ArrayType;
use Jeekens\Validation\Types\FloatType;
use Jeekens\Validation\Types\IntegerType;
use Jeekens\Validation\Types\NumericType;
use Jeekens\Validation\Types\ObjectType;
use Jeekens\validation\Types\StringType;
use Jeekens\Validation\Types\TypeRuleInterface;
use function explode;
use function in_array;
use function is_array;
use function is_string;
use function preg_match;
use function sprintf;
use function str_replace;
use function strpos;

/**
 * Class Validate
 *
 * @package Jeekens\Validation
 */
class Validate implements ValidateInterface
{

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var bool[]
     */
    protected $validated = [];

    /**
     * @var string
     */
    protected $defaultType = 'string';

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @var bool
     */
    protected $result = null;

    /**
     * @var TypeRuleInterface[]
     */
    protected static $baseTypes = [];

    /**
     * @var TypeRuleInterface[]
     */
    protected static $types = [];

    /**
     * @var string[]
     */
    protected static $typeToBase = [];

    /**
     * @var string[]
     */
    protected static $ruleType = [];

    /**
     * @var RuleInterface[]
     */
    protected static $validate = [];

    /**
     * @var array
     */
    protected $required = [];

    /**
     * @var array
     */
    protected $confirmed = [];

    /**
     * @var bool[]
     */
    protected $empty = [];

    /**
     * @var string[]
     */
    protected $dataType = [];

    /**
     * @var ErrorBag
     */
    protected $errorBag;

    /**
     * @var string[]
     */
    protected $requiredCon = [
        'required',
        'required_if',
        'required_unless',
        'required_with',
        'required_out',
        'required_with_all',
        'required_out_all',
        'required_with_if',
        'required_out_if',
        'required_with_all_if',
        'required_out_all_if',
        'required_with_unless',
        'required_out_unless',
        'required_with_all_unless',
        'required_out_all_unless',
    ];

    /**
     * @var string[]
     */
    protected $confirmCon = [
        'confirm',
        'confirm_in',
        'confirm_not',
        'confirm_if',
        'confirm_unless',
        'confirm_in_if',
        'confirm_in_unless',
        'confirm_not_if',
        'confirm_not_unless',
    ];

    /**
     * @var string[]
     */
    protected $sizeCon = [
        'eq',
        'neq',
        'gt',
        'egt',
        'lt',
        'elt',
        'size',
        'between',
        'not_between',
    ];

    /**
     * @var string[]
     */
    protected $emptyCon = [
        'empty',
        'not_empty',
    ];

    /**
     * @var string[]
     */
    protected $comCon = [
        'in',
        'not_in',
        'regex',
        'not_regex',
    ];

    /**
     * Validate constructor.
     *
     * @param array|null $data
     * @param array|null $rules
     * @param array|null $messages
     *
     * @throws InvalidRuleException
     */
    public function __construct(?array $data = null, ?array $rules = null, ?array $messages = null)
    {
        if (!empty($data)) {
            $this->setData($data);
        }

        if (!empty($rules)) {
            $this->setRules($rules);
        }

        if (!empty($messages)) {
            $this->setMessages($messages);
        }

        if (empty(self::$baseTypes)) {
            $this->initValidationTyped();
        }

        if (! empty($data) && empty($rules)) {
            $this->validate();
        }
    }


    protected function validate(): bool
    {
        if ($this->result !== null) {
            if (empty($this->data) && empty($this->rules)) {
                $this->result = true;
                return true;
            }

            // 拍平所有多维数组
            $data = Arr::dot($this->data);
            $subData = [];
            $subRules = [];
            $fRules = [];

            foreach ($data as $name => $item) {
                if (strpos($name, '.')) {
                    $subData[$name] = [
                        '/^'.str_replace('*', '(?:.*?)(?!\.)', str_replace('.', '\.', $name)).'$/',
                        $item
                    ];
                }
            }

            foreach ($this->rules as $name => $item) {
                if (strpos($name, '.')) {
                    $subRules[$name] = $item;
                } else {
                    $rules[$name] = $item;
                }
            }

            foreach ($fRules as $field => $rules) {
                if ($this->validated[$field]) {
                    continue;
                }

                $type = $this->dataType[$field] ?? $this->defaultType;
                $typeCheck = self::$types[$type] ?? self::$baseTypes[$type];
                $required = $this->checkRequired($field);
                $confirm = $this->checkConfirm($field);
                $nullable = $this->checkEmpty($field);
                $data = $this->data[$field] ?? null;
                $isEmpty = $typeCheck->isEmpty($data);

                if ($required && $data === null) {
                    $this->addError($field, 'required');
                    $this->validated[$field] = false;
                    continue;
                }

                if (!$nullable && $isEmpty) {
                    $this->addError($field, 'empty');
                    $this->validated[$field] = false;
                    continue;
                }

                if ($nullable && $isEmpty) {
                    $this->validated[$field] = true;
                    continue;
                }

                if (!$typeCheck->check($data)) {
                    $this->addError($field, 'type');
                    $this->validated[$field] = false;
                    continue;
                }

                if ($confirm && !$this->confirmCheck($field)) {
                    $this->addError($field, 'confirm');
                    $this->validated[$field] = false;
                    continue;
                }

                foreach ($rules as $rule) {
                    if ($this->validateField($rule, $field, $data) === false) {
                        $this->validated[$field] = false;
                        continue;
                    }
                }
            }

            foreach ($subRules as $field => $rules) {
                $type = $this->dataType[$field] ?? $this->defaultType;
                $typeCheck = self::$types[$type] ?? self::$baseTypes[$type];
                $required = $this->checkRequired($field);
                $confirm = $this->checkConfirm($field);
                $nullable = $this->checkEmpty($field);
                $validate = false;
                $tmp = [];

                foreach ($subData as $index => $data) {
                    if (preg_match($rules[0], $index)) {
                        $validate = true;
                        if ($required && $data === null) {
                            $this->addError($field, 'required', $index);
                            $tmp[] = false;
                            continue 1;
                        }

                        if (!$nullable && $isEmpty) {
                            $this->addError($field, 'empty', $index);
                            $tmp[] = false;
                            continue 1;
                        }

                        if ($nullable && $isEmpty) {
                            $tmp[] = true;
                            continue 1;
                        }

                        if (!$typeCheck->check($data)) {
                            $this->addError($field, 'type', $index);
                            $tmp[] = false;
                            continue 1;
                        }

                        if ($confirm && !$this->confirmCheck($field)) {
                            $this->addError($field, 'confirm', $index);
                            $tmp[] = false;
                            continue 1;
                        }

                        foreach ($rules as $rule) {
                            $tmp[] = $this->validateField($rule, $field, $data);
                        }
                    }
                }

                if ($validate === false) {

                    if ($required) {
                        $this->addError($field, 'required', $field);
                        $this->validated[$field] = false;
                    } else {
                        $this->validated[$field] = true;
                    }

                } else {
                    $validate = false;
                }
            }

            if (in_array(false, $this->validated, true)) {
                $this->result = false;
            } else {
                $this->result = true;
            }
        }

        return $this->result;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param array $rules
     *
     * @return $this
     *
     * @throws InvalidRuleException
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;

        if ($this->result === null) {
            $this->scanRules();
        }

        return $this;
    }

    /**
     * @param array $messages
     *
     * @return $this
     */
    public function setMessages(array $messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @return ErrorBag
     */
    public function errorBag()
    {
        if (!($this->errorBag instanceof ErrorBag)) {
            $this->errorBag = new ErrorBag();
        }
        return $this->errorBag;
    }

    /**
     * 扫描并格式化验证规则
     *
     * @throws InvalidRuleException
     */
    protected function scanRules()
    {
        foreach ($this->rules as $name => $rules) {

            if (is_string($rules)) {
                $rules = explode('|', $rules);
            } elseif (!is_array($rules)) {
                throw new InvalidRuleException('"%s" rules must be string or array.', $name);
            }

            foreach ($rules as $index => $rule) {
                if (strpos(':', $rule)) {
                    [$rule, $args] = explode(':', $rule, 2);
                    if (strpos(',', $args) !== false) {
                        $args = explode(',', $args);
                    } else {
                        $args = [$args];
                    }
                }

                if (is_array($rule)) {
                    $args = array_slice($rule, 1);
                    $rule = $rule[0];
                }

                if (isset($args) && $this->checkCon($name, $rule, $args)) {
                    continue 1;
                } elseif ($this->checkCon($name, $rule)) {
                    continue 1;
                }

                if (isset($args)) {
                    $rules[$index] = [$rule, $args];
                    $args = null;
                } else {
                    $rules[$index] = $rule;
                }
            }

            $this->rules[$name] = $rules;
        }
    }

    protected function checkCon(string $name, string $rule, ?array $args = null): bool
    {
        $isArgsNull = $args === null;

        if (isset(self::$types[$rule]) || isset(self::$baseTypes[$rule])) {
            $isArgsNull ? $this->dataType[$name] = $rule : $this->dataType[$name] = [$rule, $args];
            return true;
        }

        if (in_array($rule, $this->requiredCon)) {
            $isArgsNull ? $this->required[$name] = $rule : $this->required[$name] = [$rule, $args];
            return true;
        }

        if (in_array($rule, $this->confirmCon)) {
            $isArgsNull ? $this->confirmed[$name] = $rule : $this->confirmed[$name] = [$rule, $args];
            return true;
        }

        if (in_array($rule, $this->emptyCon)) {
            $isArgsNull ? $this->empty[$name] = $rule : $this->empty[$name] = [$rule, $args];
            return true;
        }

        return  false;
    }

    /**
     * 初始化验证规则和验证类型
     */
    protected function initValidationTyped()
    {
        Validation::init();
        $rules = Validation::getRules();

        self::$baseTypes = [
            'string' => new StringType(),
            'object' => new ObjectType(),
            'numeric' => new NumericType(),
            'integer' => new IntegerType(),
            'float' => new FloatType(),
            'array' => new ArrayType(),
        ];

        foreach ($rules as $rule) {
            $isTyped = false;
            $ruleName = $rule->getName();
            $typed = $rule->bindTyped();

            if ($rule instanceof TypeRuleInterface) {
                $isTyped = true;
                self::$types[$ruleName] = $rule;
            } else {
                self::$validate[$ruleName] = $rule;
            }


            if ($isTyped) {
                if (!empty($typed)) {
                    self::$typeToBase[$ruleName] = $typed;
                }
            } else {
                if (empty($typed)) {
                    throw new InvalidArgumentException(sprintf('Rule "%s"\'s type is empty.', $ruleName));
                }
                self::$ruleType[$ruleName] = $typed;
            }
        }

        $this->checkRulesTyped();
    }

    /**
     * 验证规则是否合法
     */
    protected function checkRulesTyped()
    {
        foreach (self::$typeToBase as $type => $base) {
            if (!isset(self::$baseTypes[$base])) {
                throw new InvalidArgumentException(sprintf('Type "%s" is invalid.', $type));
            }
        }

        foreach (self::$ruleType as $ruleName => $typedName) {
            if (!(isset(self::$types[$typedName]) || isset(self::$baseTypes[$typedName]))) {
                throw new InvalidArgumentException(sprintf('Rule "%s"\'s %s type invalid.', $ruleName, $typedName));
            }
        }
    }

}