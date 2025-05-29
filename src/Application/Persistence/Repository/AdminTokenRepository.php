<?php

namespace DemoShop\Application\Persistence\Repository;

use DateTime;
use DemoShop\Application\BusinessLogic\RepositoryInterface\AdminTokenRepositoryInterface;
use DemoShop\Application\Persistence\Model\AdminToken;
use Illuminate\Support\Carbon;

/**
 * Repository implementation for managing admin authentication tokens.
 */
class AdminTokenRepository implements AdminTokenRepositoryInterface
{
    /**
     * Stores a new admin token with expiration.
     *
     * @param int $adminId The ID of the admin.
     * @param string $token The token string.
     * @param DateTime $expiresAt The expiration datetime of the token.
     *
     * @return void
     */
    public function storeToken(int $adminId, string $token, Datetime $expiresAt): void
    {
        AdminToken::create([
            'admin_id' => $adminId,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
    }

    /**
     * Finds the admin ID associated with a valid (non-expired) token.
     *
     * @param string $token The token to search for.
     *
     * @return int|null The admin ID if the token is found and valid; null otherwise.
     */
    public function findAdminIdByToken(string $token): ?int
    {
        $record = AdminToken::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        return $record ? $record->admin_id : null;
    }

    /**
     * Deletes a token from the database.
     *
     * @param string $token The token to delete.
     *
     * @return void
     */
    public function deleteToken(string $token): void
    {
        AdminToken::where('token', $token)->delete();
    }

}