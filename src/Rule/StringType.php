<?php declare(strict_types=1);


namespace Jeekens\validation\Rule;


use Exception;
use Throwable;
use DateTimeZone;
use Jeekens\Basics\Str;
use Jeekens\Basics\Json;
use Jeekens\validation\TypeRule;
use Jeekens\Basics\Exception\JsonDecodeException;

class StringType extends TypeRule
{

    protected $format = [
        'alpha' => 'alphaCheck',
        'alpha_dash' => 'alphaDashCheck',
        'alpha_num' => 'alphaNumCheck',
        'email' => 'emailCheck',
        'ip' => 'ipCheck',
        'ipv4' => 'ipv4Check',
        'ipv6' => 'ipv6Check',
        'url' => 'urlCheck',
        'mac' => 'macCheck',
        'json' => 'jsonCheck',
        'timezone' => 'timezoneCheck',
        'domain' => 'domainCheck',
    ];

    protected $ruleName = 'string';

    public function check($value): bool
    {
        return is_string($value) || is_numeric($value);
    }

    public function getSize($value, ?string $format): int
    {
        return Str::length($value);
    }

    public function getDefaultFormat(): ?string
    {
        return null;
    }

    public function alphaCheck($value): bool
    {
        return preg_match('/^[\pL\pM]+$/u', $value);
    }

    public function alphaDashCheck($value): bool
    {
        return preg_match('/^[\pL\pM\pN_-]+$/u', $value) > 0;
    }

    public function alphaNumCheck($value): bool
    {
        return preg_match('/^[\pL\pM\pN]+$/u', $value) > 0;
    }

    public function emailCheck($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function ipCheck($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP);
    }

    public function ipv4Check($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    public function ipv6Check($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    public function urlCheck($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    public function macCheck($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_MAC) !== false;
    }

    public function jsonCheck($value): bool
    {
        try {
            Json::decode($value);
        } catch (JsonDecodeException $e) {
            return false;
        }

        return true;
    }

    public function domainCheck($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }

    public function timezoneCheck($value): bool
    {
        try {
            new DateTimeZone($value);
        } catch (Exception $e) {
            return false;
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }


}