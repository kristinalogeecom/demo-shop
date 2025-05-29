<?php

namespace DemoShop\Infrastructure\Security;

use DateTime;
use DemoShop\Application\BusinessLogic\Encryption\EncryptionInterface;
use DemoShop\Infrastructure\Exception\InvalidCookieException;
use Throwable;

/**
 * Manages setting, retrieving, and clearing HTTP cookies,
 * with support for encrypted session storage.
 *
 * Handles secure cookie options (e.g. HttpOnly, SameSite, Secure),
 * and adapts behavior based on the environment (localhost vs production).
 */
class CookieManager
{
    private EncryptionInterface $encryption;

    /**
     * Initializes the CookieManager with a given encryption implementation.
     *
     * @param EncryptionInterface $encryption The encryption mechanism for session cookies.
     */
    public function __construct(EncryptionInterface $encryption)
    {
        $this->encryption = $encryption;
    }


    /**
     * Sets a persistent cookie (for "remember me" tokens).
     *
     * @param string   $name    The cookie name.
     * @param string   $value   The raw value to store.
     * @param DateTime $expires The expiration datetime.
     *
     * @return void
     */
    public function setPersistentToken(string $name, string $value, DateTime $expires): void
    {
        setcookie($name, $value, [
            'expires' => $expires->getTimestamp(),
            'path' => '/',
            'secure' => !$this->isLocalhost(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Clears a cookie by setting an expiration time in the past.
     *
     * @param string $name The name of the cookie to clear.
     *
     * @return void
     */
    public function clearCookie(string $name): void
    {
        setcookie($name, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => !$this->isLocalhost(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Sets an encrypted session cookie with structured data.
     *
     * The data is JSON-encoded, encrypted, and stored as a cookie value.
     *
     * @param string   $name    The cookie name.
     * @param array    $data    The associative array to store.
     * @param DateTime $expires Expiration time of the cookie.
     *
     * @return void
     */
    public function setEncryptedSession(string $name, array $data, DateTime $expires): void
    {
        $encrypted = $this->encryption->encrypt(json_encode($data));
        setcookie($name, $encrypted, [
            'expires' => $expires->getTimestamp(),
            'path' => '/',
            'secure' => !$this->isLocalhost(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Retrieves and decrypts session data from a cookie.
     *
     * @param string $name The name of the cookie to read.
     *
     * @return array|null The decrypted session data, or null on failure.
     *
     * @throws InvalidCookieException
     */
    public function getDecryptedSession(string $name): ?array
    {
        $encrypted = $_COOKIE[$name] ?? null;
        if (!$encrypted) return null;

        try {
            $json = $this->encryption->decrypt($encrypted);
            $data = json_decode($json, true);

            if (!is_array($data)) {
                throw new InvalidCookieException($name);
            }

            return $data;
        } catch (Throwable) {
            throw new InvalidCookieException($name);
        }
    }

    /**
     * Retrieves the raw (unencrypted) value of a cookie.
     *
     * @param string $name The cookie name.
     *
     * @return string|null The cookie value or null if not set.
     */
    public function getCookie(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
     * Determines if the application is running in a localhost environment.
     *
     * This affects whether the 'secure' flag is enabled for cookies.
     *
     * @return bool True if on localhost or 127.0.0.1, false otherwise.
     */
    private function isLocalhost(): bool
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        return str_contains($host, 'localhost') || str_contains($host, '127.0.0.1');
    }
}
