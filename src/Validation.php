<?php


namespace Jeekens\Validation;


class Validation
{

    /**
     * @var ErrorBag
     */
    protected $errorBag = null;

    /**
     * Validation constructor.
     */
    public function __construct()
    {
        $this->errorBag = new ErrorBag();
    }

    /**
     * @param string $key
     * @param string $message
     */
    public function addError(string $key, string $message)
    {
        $this->errorBag->add($key, $message);
    }

    /**
     * @return bool
     */
    public function passes(): bool
    {
        return $this->errorBag->isEmpty();
    }

    /**
     * @return bool
     */
    public function fails(): bool
    {
        return $this->errorBag->isNotEmpty();
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->has($key);
    }

    /**
     * @return ErrorBag
     */
    public function getError(): ErrorBag
    {
        return $this->errorBag;
    }

}