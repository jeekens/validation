<?php


namespace Jeekens\Validator\Rule;


use Jeekens\Validator\TypeRule;

class DateType extends TypeRule
{

    public function getSize($value, ?string $format): int
    {
        // TODO: Implement getSize() method.
    }

    public function check($value): bool
    {
        // TODO: Implement check() method.
    }

    public function formatCheck($value, ?string $format): bool
    {
        // TODO: Implement formatCheck() method.
    }

    public function getDefaultFormat(): ?string
    {
        return 'Y-m-d H:i:s';
    }

}