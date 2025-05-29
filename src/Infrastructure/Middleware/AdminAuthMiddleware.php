<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Application\BusinessLogic\RepositoryInterface\AdminTokenRepositoryInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Security\CookieManager;
use Exception;

/**
 * Middleware that restricts access to admin-only routes.
 *
 * Checks for a valid long-term or short-term authentication cookie.
 * If neither is found nor valid, throws an authorization exception.
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
        $cookieManager = ServiceRegistry::get(CookieManager::class);

        $cookieToken = $cookieManager->getCookie('admin_token');
        if ($cookieToken) {
            $tokenRepository = ServiceRegistry::get(AdminTokenRepositoryInterface::class);
            $adminId = $tokenRepository->findAdminIdByToken($cookieToken);

            if($adminId) {
                return;
            }
        }

        $session = $cookieManager->getDecryptedSession('admin_session');

        if (
            isset($session['admin_id'], $session['exp']) &&
            time() < $session['exp']
        ) {
            return;
        }

        header('Location: /admin/login?error=unauthorized');
        exit;
    }
}