<?php

namespace DemoShop\Infrastructure\Exception;

class ServiceNotFoundException extends Exception
{
    public function __construct(string $serviceKey)
    {
        parent::__construct("Service '$serviceKey' not found in the registry.");
    }
}