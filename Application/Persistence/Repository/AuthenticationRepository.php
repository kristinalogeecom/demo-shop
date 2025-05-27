<?php

namespace DemoShop\Application\Persistence\Repository;

use DemoShop\Application\BusinessLogic\Encryption\EncryptionInterface;
use DemoShop\Application\BusinessLogic\RepositoryInterface\AuthenticationRepositoryInterface;
use DemoShop\Application\Persistence\Model\Admin;

/**
 * Repository for handling admin authentication logic,
 * including user lookup and password verification via encryption.
 */
class AuthenticationRepository implements AuthenticationRepositoryInterface
{
    /**
     * Encryption service for secure credential handling.
     *
     * @var EncryptionInterface
     */
    private EncryptionInterface $encryption;

    public function __construct(EncryptionInterface $encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * Finds an admin by decrypted username.
     *
     * @param string $username The plain-text username to match.
     *
     * @return Admin|null The matching admin model or null if not found.
     */
    public function findByUsername(string $username): ?Admin
    {
        foreach (Admin::all() as $admin) {
            if ($this->encryption->decrypt($admin->username) === $username) {
                return $admin;
            }
        }
        return null;
    }

    /**
     * Verifies the given plain-text password against the encrypted one in the model.
     *
     * @param Admin $admin The admin whose password to check.
     * @param string $password The plain-text password provided by the user.
     *
     * @return bool True if the password matches, false otherwise.
     */
    public function verifyPassword(Admin $admin, string $password): bool
    {
        try {
            $decryptedPassword = $this->encryption->decrypt($admin->password);
            return $decryptedPassword === $password;
        } catch (\Exception $e) {
            return false;
        }
    }
}