<?php

namespace DemoShop\Infrastructure\Exception;

class ControllerNotFoundException extends Exception
{
    public function __construct(string $controllerClass)
    {
        parent::__construct("Controller class '$controllerClass' does not exist.");
    }
}