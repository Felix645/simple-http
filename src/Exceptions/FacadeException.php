<?php


namespace Neon\Http\Exceptions;


use Exception;
use Throwable;


class FacadeException extends Exception
{
    /**
     * FacadeException constructor.
     *
     * @param string $accessor
     * @param string $method
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($accessor, $method, $code = 0, Throwable $previous = null)
    {
        $message = "Method '$method' does not exists in class $accessor";
        parent::__construct($message, $code, $previous);
    }
}