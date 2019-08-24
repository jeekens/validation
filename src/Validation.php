<?php declare(strict_types=1);


namespace Jeekens\Validation;


class Validation implements ValidationInterface
{

    /**
     * @var RuleInterface[]
     */
    protected static $rules = [];

    /**
     * @var bool
     */
    protected static $isInit = false;

    /**
     * @param string $name
     * @param RuleInterface $rule
     */
    public static function addRule(string $name, RuleInterface $rule)
    {
        self::$rules[$name] = $rule;
    }

    /**
     * @return RuleInterface[]
     */
    public static function getRules(): array
    {
        return self::$rules;
    }

    /**
     * @param array|null $data
     * @param array|null $rules
     * @param array|null $messages
     *
     * @return ValidateInterface
     */
    public static function make(?array $data = null, ?array $rules = null, ?array $messages = null): ValidateInterface
    {
        return new Validate($data, $rules, $messages);
    }


    public static function init()
    {
        if (self::$isInit) {
            return;
        }

        self::$isInit = true;
    }

}