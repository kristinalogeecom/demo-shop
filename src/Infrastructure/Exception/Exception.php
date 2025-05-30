<?php

namespace DemoShop\Infrastructure\Exception;

use Throwable;

/**
 * Base class for custom infrastructure-related exceptions.
 */
abstract class Exception extends \Exception
{
    /**
     * Exception constructor.
     *
     * @param string         $message  Exception message
     * @param int            $code     Exception code (optional)
     * @param Throwable|null $previous Previous throwable (optional)
     */
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
