<?php

namespace DemoShop\Infrastructure\Session;

/**
 * Manages access to PHP session using Singleton pattern.
 *
 * Provides a centralized way to interact with the $_SESSION superglobal
 */
class SessionManager
{
    /**
     * @var self|null The singleton instance.
     */
    private static ?self $instance = null;

    /**
     * Private constructor ensures session is started once and prevents external instantiation.
     */
    private function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Returns the singleton instance of SessionManager.
     *
     * @return SessionManager The session manager instance
     */
    public static function getInstance(): SessionManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Retrieves a value from the session by key.
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Stores a value in the session under the given key.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Removes a value from the session by key.
     *
     * @param string $key
     * @return void
     */
    public function unset(string $key): void
    {
        unset($_SESSION[$key]);
    }
}