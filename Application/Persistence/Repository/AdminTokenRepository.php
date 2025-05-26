<?php

namespace DemoShop\Application\Persistence\Repository;

use DemoShop\Application\Persistence\Model\AdminToken;
use Illuminate\Support\Carbon;

class AdminTokenRepository
{
    public function storeToken(int $adminId, string $token, \Datetime $expiresAt): void
    {
        echo "STORE TOKEN CALLED";
        AdminToken::create([
            'admin_id' => $adminId,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
    }

    public function findAdminIdByToken(string $token): ?int
    {
        $tokenRecord = AdminToken::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        return $tokenRecord ? $tokenRecord->admin_id : null;
    }
}