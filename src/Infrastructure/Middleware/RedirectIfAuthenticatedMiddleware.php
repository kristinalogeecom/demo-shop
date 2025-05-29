<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Application\BusinessLogic\RepositoryInterface\AdminTokenRepositoryInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Security\CookieManager;
use Exception;

/**
 * Redirects authenticated users from login page to dashboard.
 */
class RedirectIfAuthenticatedMiddleware extends Middleware
{
    /**
     * @throws Exception
     */
    protected function handle(Request $request): void
    {
        $cookieManager = ServiceRegistry::get(CookieManager::class);

        $cookieToken = $cookieManager->getCookie('admin_token');
        if ($cookieToken) {
            $tokenRepo = ServiceRegistry::get(AdminTokenRepositoryInterface::class);
            if ($tokenRepo->findAdminIdByToken($cookieToken)) {
                header('Location: /admin/dashboard');
                exit;
            }
        }

        $session = $cookieManager->getDecryptedSession('admin_session');
        if (
            isset($session['admin_id'], $session['exp']) &&
            time() < $session['exp']
        ) {
            header('Location: /admin/dashboard');
            exit;
        }

    }
}
