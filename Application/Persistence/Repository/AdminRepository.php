<?php

namespace DemoShop\Application\Persistence\Repository;

use DemoShop\Application\BusinessLogic\Encryption\EncryptionInterface;
use DemoShop\Application\Persistence\Model\Admin;

/**
 * Responsible for retrieving and validating admin user data
 * from the database using encrypted fields.
 */
class AdminRepository
{
    private EncryptionInterface $encryption;

    /**
     * @param EncryptionInterface $encryption The encryption service used to decrypt credentials.
     */
    public function __construct(EncryptionInterface $encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * Finds an admin by decrypted username.
     *
     * Iterates through all admin records, decrypts each username,
     * and compares it to the input value.
     *
     * @param string $username The plain-text username to search for.
     *
     * @return Admin|null The matching admin model, or null if not found.
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
     * Verifies whether the provided password matches the stored encrypted one.
     *
     * @param Admin $admin The admin user whose password is being verified.
     * @param string $password The plain-text password to compare.
     *
     * @return bool True if passwords match; false otherwise or if decryption fails.
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