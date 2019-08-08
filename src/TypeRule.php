<?php


namespace Jeekens\Validator;


use Jeekens\Validation\TypeRuleInterface;
use Jeekens\Validator\Exception\UnsupportedFormatVerificationException;

abstract class TypeRule implements TypeRuleInterface
{


    protected $format = null;


    public function compare($value, string $condition, array $attribute): bool
    {
        return $this->$condition($value, $attribute);
    }

    protected function in($value, $array)
    {
        return array_search($value, $array) !== false;
    }

    protected  function notIn($value, $array)
    {
        return array_search($value, $array) === false;
    }

    /**
     * @param $value
     * @param string $format
     *
     * @return bool
     *
     * @throws UnsupportedFormatVerificationException
     */
    public function formatCheck($value, ?string $format): bool
    {
        if (! empty($format) && ($method = $this->format[$format]) && method_exists($this, $method)) {
            return $this->$method($value);
        } else {
            throw new UnsupportedFormatVerificationException(sprintf('Rule is unsupported "%s" format!', $format));
        }
    }

}