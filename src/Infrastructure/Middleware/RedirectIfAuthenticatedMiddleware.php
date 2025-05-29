<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Application\BusinessLogic\RepositoryInterface\AdminTokenRepositoryInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\ResponseException;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\RedirectResponse;
use DemoShop\Infrastructure\Security\CookieManager;
/**
 * Redirects authenticated users from login page to dashboard.
 */
class RedirectIfAuthenticatedMiddleware extends Middleware
{
    /**
     * If the user is already authenticated, redirects to the dashboard.
     *
     * @param Request $request
     *
     * @return void
     *
     * @throws ResponseException
     */
    protected function handle(Request $request): void
    {
        $cookieManager = ServiceRegistry::get(CookieManager::class);

        $isAuthenticated = false;

        $cookieToken = $cookieManager->getCookie('admin_token');
        if ($cookieToken) {
            $tokenRepo = ServiceRegistry::get(AdminTokenRepositoryInterface::class);
            if ($tokenRepo->findAdminIdByToken($cookieToken)) {
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

        if($isAuthenticated) {
            throw new ResponseException(new RedirectResponse('/admin/dashboard'));
        }

    }
}
