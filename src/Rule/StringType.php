<?php


namespace Jeekens\Validator\Rule;


use Jeekens\Basics\Str;
use Jeekens\Validator\TypeRule;

class StringType extends TypeRule
{

    protected $format = [
        'alpha' => 'alphaCheck',
        'alpha_dash' => 'alphaDashCheck',
        'alpha_num' => 'alphaNumCheck',
        'email' => 'emailCheck',
        'ipv4' => 'ipv4Check',
        'ipv6' => 'ipv6Check',
        'url' => 'urlCheck',
        'json' => 'jsonCheck',
        'timezone' => 'timezoneCheck',
        'phone_num' => 'phoneNumCheck',
        'tel_num' => 'telNumCheck',
    ];

    public function check($value): bool
    {
        return is_string($value);
    }

    public function getSize($value, ?string $format): int
    {
        return Str::length($value);
    }

    public function getDefaultFormat(): ?string
    {
        return null;
    }

}