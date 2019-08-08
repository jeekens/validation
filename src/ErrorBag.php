<?php declare(strict_types=1);


namespace Jeekens\Validation;


class ErrorBag
{

    protected $messages = [];


    public function __construct(?array $messages = null)
    {
        if (! empty($this->messages)) {
            $this->messages = $messages;
        }
    }

    /**
     * @param string $key
     * @param string $message
     */
    public function add(string $key, string $message)
    {
        $this->messages[$key][] = $message;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->messages);
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function firstOf(string $key): ?string
    {
        return $this->messages[$key][0] ?? null;
    }

    /**
     * @return string|null
     */
    public function first(): ?string
    {
        $first = reset($this->messages);
        return empty($first) ? null : $first[0];
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->messages;
    }

    /**
     * @return array
     */
    public function allFirst()
    {
        $tmp = [];

        foreach ($this->messages as $key => $value) {
            $tmp[$key] = $value[0];
        }

        return $tmp;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->messages);
    }

    /**
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return ! empty($this->messages);
    }

}