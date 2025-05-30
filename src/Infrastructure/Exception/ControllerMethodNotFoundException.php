<?php

namespace DemoShop\Infrastructure\Exception;

class ControllerMethodNotFoundException extends Exception
{
    public function __construct(string $controllerClass, string $method)
    {
        parent::__construct("Method '$method' does not exist in controller '$controllerClass'.");
    }
}