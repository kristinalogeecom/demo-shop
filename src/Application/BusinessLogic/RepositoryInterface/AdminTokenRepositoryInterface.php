<?php

namespace DemoShop\Application\BusinessLogic\RepositoryInterface;

use DateTime;

/**
 * Interface for managing admin authentication tokens.
 */
interface AdminTokenRepositoryInterface
{
    /**
     * Stores a new authentication token for an admin.
     *
     * @param int $adminId The ID of the admin.
     * @param string $token The authentication token.
     * @param DateTime $expiresAt The expiration date and time of the token.
     *
     * @return void
     */
    public function storeToken(int $adminId, string $token, Datetime $expiresAt): void;

    /**
     * Finds the admin ID associated with the given token.
     *
     * @param string $token The authentication token.
     *
     * @return int|null The admin ID if found, or null if the token is invalid or expired.
     */
    public function findAdminIdByToken(string $token): ?int;

    /**
     * Deletes an authentication token.
     *
     * @param string $token The token to delete.
     *
     * @return void
     */
    public function deleteToken(string $token): void;
}