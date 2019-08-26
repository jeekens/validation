<?php declare(strict_types=1);


namespace Jeekens\Validation;


use InvalidArgumentException;
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
use function sprintf;
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
     * @var string
     */
    protected $defaultMessage = '';

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

    /**
     * 对参数进行验证
     *
     * @return bool
     *
     * @throws InvalidRuleException
     */
    protected function validate(): bool
    {
        if ($this->result !== null) {
            if (empty($this->data) && empty($this->rules)) {
                $this->result = true;
                return true;
            }

            foreach ($this->rules as $field => $rules) {
                if ($this->validated[$field]) {
                    continue;
                }

                $required = $this->checkRequired($field);

                if (strpos('.', $field)) {
                    $data = data_get($this->data, $field);
                    if ($required && $data === null) {
                        $this->addError($field, 'required');
                        $this->validated[$field] = false;
                        continue;
                    }
                    $this->validated[$field] = $this->validateDotField($field, $rules, $data);
                } else {
                    $data = $this->data[$field] ?? null;
                    if ($required && $data === null) {
                        $this->addError($field, 'required');
                        $this->validated[$field] = false;
                        continue;
                    }
                    $this->validated[$field] = $this->validateField($field, $rules, $data);
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
     * 验证数据
     *
     * @param string $field
     * @param array $rules
     * @param $data
     * @param string|null $index
     *
     * @return bool
     *
     * @throws InvalidRuleException
     */
    protected function validateField(string $field, array $rules, $data, ?string $index = null): bool
    {
        $type = $this->dataType[$field] ?? $this->defaultType;
        $typeCheck = self::$types[$type] ?? self::$baseTypes[$type];
        $nullable = $this->checkEmpty($field);
        $isEmpty = $typeCheck->isEmpty($data);

        if (! $typeCheck->check($data)) {
            $this->addError($field, 'type', null, $index);
            return false;
        }

        if ($nullable && $isEmpty) {
            return true;
        }

        if (!$nullable && $isEmpty) {
            $this->addError($field, 'empty', null, $index);
            return false;
        }

        foreach ($rules as $rule) {
            $args = null;

            if (is_array($rule)) {
                $args = $rule[1];
                $rule= $rule[0];
            }

            if (($method = $typeCheck->getRuleMethod($rule)) && ! empty($method)) {
                $res =  $type->$method($rule, $data, $args);

                if ($res == false) {
                    $this->addError($field, $rule, $args, $index);
                }
                return $res;
            } elseif (in_array($rule, $this->sizeCon)) {
                $res = $this->checkSize($rule, $data, $args);

                if ($res == false) {
                    $this->addError($field, $rule, $args, $index);
                }
                return $res;
            } elseif (in_array($rule, $this->comCon)) {
                $res = $this->$rule($data, $args);

                if ($res == false) {
                    $this->addError($field, $rule, $args, $index);
                }
                return $res;
            } elseif (in_array($rule, $this->confirmCon)) {
                $res = $this->checkConfirm($rule, $data, $args);

                if ($res == false) {
                    $this->addError($field, $rule, $args, $index);
                }
                return $res;
            } else {
                throw new InvalidRuleException(sprintf('Invalid "%s" rule', $rule));
            }
        }

        return true;
    }

    /**
     * 验证带.的子集数据
     *
     * @param string $field
     * @param array $rules
     * @param $data
     *
     * @return bool
     *
     * @throws InvalidRuleException
     */
    protected function validateDotField(string $field, array $rules, $data): bool
    {
        $result = [];

        foreach ($data as $index => $item) {
            $result[] = $this->validateField($field, $rules, $data, $index);
        }

        return $result === array_filter($result, function ($res) {
            return $res;
        });
    }

    /**
     * 为字段添加一个错误信息
     *
     * @param string $field
     * @param string $rule
     * @param null $args
     * @param string|null $index
     */
    protected function addError(string $field, string $rule, $args = null, ?string $index = null)
    {
        $errorBag = $this->errorBag();
        $key = $index === null ? $field : $index;

        if ($rule === 'type') {
            $message = self::$baseTypes[$this->dataType[$field]]->getMessage() ??
                self::$types[$this->dataType[$field]]->getMessage();
        } elseif (isset($this->messages[$field]) && is_string($this->messages[$field])) {
            $message = $this->messages[$field];
        } elseif (isset($this->messages[$field][$rule]) && is_string($this->messages[$field][$rule])) {
            $message = $this->messages[$field][$rule];
        } else {
            $message = self::$validate[$rule]->getMessage();
        }

        $message = $message ?? $this->defaultMessage;
        $errorBag->add($key, $message);
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

    /**
     * 确认传入的特殊规则
     *
     * @param string $name
     * @param string $rule
     * @param array|null $args
     *
     * @return bool
     */
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