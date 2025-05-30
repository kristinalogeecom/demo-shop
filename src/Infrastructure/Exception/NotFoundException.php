<?php

namespace DemoShop\Infrastructure\Exception;

use Throwable;

class NotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message);
    }
}