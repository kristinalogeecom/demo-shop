<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Application\BusinessLogic\RepositoryInterface\AdminTokenRepositoryInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\ResponseException;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\RedirectResponse;
use DemoShop\Infrastructure\Security\CookieManager;

/**
 * Middleware that restricts access to admin-only routes.
 *
 * Checks for a valid long-term or short-term authentication cookie.
 * If neither is found nor valid, throws an authorization exception.
 */
class AdminAuthMiddleware extends Middleware
{
    /**
     *  Handles the request by verifying admin authentication cookies.
     *
     *  Accepts either:
     *  - A valid persistent token (`admin_token`) stored in the database
     *  - A short-lived session cookie (`admin_logged_in_cookie`)
     *
     * @param Request $request
     *
     * @return void
     *
     * @throws ResponseException
     * @throws ServiceNotFoundException
     */
    protected function handle(Request $request): void
    {
        $cookieManager = ServiceRegistry::get(CookieManager::class);
        $isAuthenticated = false;

        $cookieToken = $cookieManager->getCookie('admin_token');
        if ($cookieToken) {
            $tokenRepository = ServiceRegistry::get(AdminTokenRepositoryInterface::class);
            $adminId = $tokenRepository->findAdminIdByToken($cookieToken);
            if($adminId) {
                $isAuthenticated = true;
            }
        }

        if(!$isAuthenticated) {
            $session = $cookieManager->getDecryptedSession('admin_session');
            if (isset($session['admin_id'], $session['exp']) &&
                time() < $session['exp']) {
                $isAuthenticated = true;
            }
        }

        if(!$isAuthenticated) {
            throw new ResponseException(
                new RedirectResponse('/admin/login?error=unauthorized'));
        }
    }
}