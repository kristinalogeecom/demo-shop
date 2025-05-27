<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Application\Persistence\Repository\AdminTokenRepository;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use Exception;


/**
 * Middleware that restricts access to admin-only routes.
 *
 * Ensures that the admin is logged in via session,
 * otherwise throws an exception that can be caught by the controller or router layer.
 */
class AdminAuthMiddleware extends Middleware
{


    /**
     * Checks whether the admin is authenticated.
     *
     * @param Request $request The current HTTP request
     *
     * @return void
     *
     * @throws Exception if admin is not authenticated
     */
    protected function handle(Request $request): void
    {
        $cookieToken = $_COOKIE['admin_token'] ?? null;
        if ($cookieToken) {
            $tokenRepository = ServiceRegistry::get(AdminTokenRepository::class);
            $adminId = $tokenRepository->findAdminIdByToken($cookieToken);

            if($adminId) {
                return;
            }
        }

        $shortCookie = $_COOKIE['admin_logged_in_cookie'] ?? null;
        if($shortCookie === 'true') {
            return;
        }

        throw new Exception('Unauthorized. Please log in first.');
    }
}