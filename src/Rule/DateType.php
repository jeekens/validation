<?php declare(strict_types=1);


namespace Jeekens\validation\Rule;

use DateTime;
use Jeekens\validation\TypeRule;

class DateType extends TypeRule
{

    protected $ruleName = 'date';

    public function getSize($value, ?string $format): int
    {
        return strtotime($value);
    }

    public function check($value): bool
    {
        $date = date_parse($value);
        return checkdate($date['month'], $date['day'], $date['year']);
    }

    public function formatCheck($value, ?string $format): bool
    {
        $date = DateTime::createFromFormat('!'.$format, $value);
        return $date && $date->format($format) == $value;
    }

    public function getDefaultFormat(): ?string
    {
        return 'Y-m-d H:i:s';
    }

}