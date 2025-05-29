<?php

namespace DemoShop\Application\BusinessLogic\RepositoryInterface;

use DemoShop\Application\Persistence\Model\Admin;

/**
 * Interface for admin authentication logic.
 */
interface AuthenticationRepositoryInterface
{

    /**
     * Finds an admin by their username.
     *
     * @param string $username
     *
     * @return Admin|null The matching admin or null if not found.
     */
    public function findByUsername(string $username): ?Admin;

    /**
     * Verifies that the given password matches the stored password for the admin.
     *
     * @param Admin $admin The admin instance.
     * @param string $password The plain-text password to verify.
     *
     * @return bool True if the password is correct, false otherwise.
     */
    public function verifyPassword(Admin $admin, string $password): bool;
}