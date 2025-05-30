<?php

namespace DemoShop\Infrastructure\Exception;

class InvalidCookieException extends Exception
{
    public function __construct(string $cookieName)
    {
        parent::__construct("Failed to decrypt or parse cookie: $cookieName");
    }
}