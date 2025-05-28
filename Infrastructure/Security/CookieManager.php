<?php

namespace DemoShop\Infrastructure\Security;

use DateTime;
use DemoShop\Application\BusinessLogic\Encryption\EncryptionInterface;

class CookieManager
{
    private EncryptionInterface $encryption;

    public function __construct(EncryptionInterface $encryption)
    {
        $this->encryption = $encryption;
    }

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

    public function getDecryptedSession(string $name): ?array
    {
        $encrypted = $_COOKIE[$name] ?? null;
        if (!$encrypted) return null;

        try {
            $json = $this->encryption->decrypt($encrypted);
            return json_decode($json, true);
        } catch (\Throwable) {
            return null;
        }
    }
    public function getCookie(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    private function isLocalhost(): bool
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        return str_contains($host, 'localhost') || str_contains($host, '127.0.0.1');
    }
}
