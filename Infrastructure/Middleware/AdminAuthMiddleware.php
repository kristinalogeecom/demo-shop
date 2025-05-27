<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Application\BusinessLogic\Encryption\EncryptionInterface;
use DemoShop\Application\BusinessLogic\RepositoryInterface\AdminTokenRepositoryInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use Exception;

/**
 * Middleware that restricts access to admin-only routes.
 *
 * Checks for a valid long-term or short-term authentication cookie.
 * If neither is found or valid, throws an authorization exception.
 */
class AdminAuthMiddleware extends Middleware
{
    /**
     * Handles the request by verifying admin authentication cookies.
     *
     * Accepts either:
     * - A valid persistent token (`admin_token`) stored in the database
     * - A short-lived session cookie (`admin_logged_in_cookie`)
     *
     * @param Request $request The current HTTP request.
     *
     * @return void
     *
     * @throws Exception If the admin is not authenticated.
     */
    protected function handle(Request $request): void
    {
        $cookieToken = $_COOKIE['admin_token'] ?? null;
        if ($cookieToken) {
            $tokenRepository = ServiceRegistry::get(AdminTokenRepositoryInterface::class);
            $adminId = $tokenRepository->findAdminIdByToken($cookieToken);

            if($adminId) {
                return;
            }
        }

        $encrypted = $_COOKIE['admin_session'] ?? null;
        if ($encrypted) {
            try {
                $encryption = ServiceRegistry::get(EncryptionInterface::class);
                $data = json_decode($encryption->decrypt($encrypted), true);

                if (
                    isset($data['admin_id'], $data['exp']) &&
                    time() < $data['exp']
                ) {
                    return;
                }
            } catch (\Throwable) {
                // Fallthrough to unauthorized
            }
        }

        throw new Exception('Unauthorized. Please log in first.');
    }
}