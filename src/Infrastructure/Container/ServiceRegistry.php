<?php

namespace DemoShop\Infrastructure\Container;

use DemoShop\Infrastructure\Exception\ServiceNotFoundException;

class ServiceRegistry
{
    private static array $services = [];

    /**
     * Registers the service in the registry.
     *
     * @param string $key
     * @param object $service
     *
     * @return void
     */
    public static function set(string $key, object $service): void
    {
        self::$services[$key] = $service;
    }

    /**
     * Checking if the services are initialized
     * Returns the service from the registry
     *
     * @param string $key
     *
     * @return object
     *
     * @throws ServiceNotFoundException
     */
    public static function get(string $key): object
    {
        if (!isset(self::$services[$key])) {
            throw new ServiceNotFoundException($key);
        }

        return self::$services[$key];
    }

}