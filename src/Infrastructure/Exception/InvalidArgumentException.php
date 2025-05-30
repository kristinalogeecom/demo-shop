<?php

namespace DemoShop\Infrastructure\Exception;

use Throwable;

class InvalidArgumentException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct("'$message' must be defined.");
    }
}