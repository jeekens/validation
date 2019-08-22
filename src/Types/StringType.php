<?php declare(strict_types=1);


namespace Jeekens\validation\Types;


use Jeekens\Validation\TypeInterface;

class StringType implements TypeInterface
{

    public function check($value)
    {
        return $this->checkType($value);
    }


    public function checkType($value)
    {
        return is_string($value) || is_numeric($value);
    }

    public function getErrMsg() : ?string
    {
        // TODO: Implement getErrMsg() method.
    }

    public function compare($input): int
    {
        // TODO: Implement compare() method.
    }

}