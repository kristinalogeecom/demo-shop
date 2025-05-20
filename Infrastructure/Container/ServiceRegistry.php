<?php

namespace DemoShop\Infrastructure\Container;

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
     * @throws \Exception
     */
    public static function get(string $key): object
    {
        if (!isset(self::$services[$key])) {
            throw new \Exception("Service '{$key}' not found");
        }

        return self::$services[$key];
    }

}