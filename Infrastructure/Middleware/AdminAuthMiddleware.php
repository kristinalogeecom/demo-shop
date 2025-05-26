<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Application\Persistence\Repository\AdminTokenRepository;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Session\SessionManager;
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
        $session = SessionManager::getInstance();

        if ($request->url() === '/admin/login' && $request->method() === 'POST') {
            return;
        }

        if ($session->get('admin_logged_in')) {
            return;
        }

        $cookieToken = $_COOKIE['admin_token'] ?? null;

        if ($cookieToken) {
            $tokenRepo = ServiceRegistry::get(AdminTokenRepository::class);
            $adminId = $tokenRepo->findAdminIdByToken($cookieToken);

            if ($adminId) {
                $session->set('admin_logged_in', true);
                return;
            }
        }

        throw new Exception('Unauthorized. Please log in first.');
    }
}