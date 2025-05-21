<?php

namespace DemoShop\Infrastructure\Database;

use RuntimeException;

class DatabaseConnection
{
    private static ?PDO $instance = null;

    /**
     * @param string $key
     * @return string
     */
    private static function getRequiredEnv(string $key): string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false || $value === null || $value === '') {
            throw new RuntimeException("Environment variable '$key' not set or is empty.");
        }

        return $value;
    }

    /**
     * Connects to the database
     *
     * @return PDO
     */
    public static function connect(): PDO
    {
        if (self::$instance === null) {

            $host = self::getRequiredEnv('DB_HOST');
            $dbName = self::getRequiredEnv('DB_DATABASE');
            $user = self::getRequiredEnv('DB_USERNAME');
            $password = self::getRequiredEnv('DB_PASSWORD');
            $port     = self::getRequiredEnv('DB_PORT');

            $dsn = "mysql:host=$host;port=$port;dbname=$dbName";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $password, $options);
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage());
            }
        }

        return self::$instance;
    }

}