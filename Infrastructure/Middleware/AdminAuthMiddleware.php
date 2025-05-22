<?php

namespace DemoShop\Infrastructure\Middleware;

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

        // Allow login form to go through without being authenticated
        if ($request->url() === '/admin/login' && $request->method() === 'POST') {
            return;
        }

        // Block access to other admin routes if not logged in
        if(empty($session->get('admin_logged_in'))) {
            throw new Exception('Unauthorized. Please log in first.');
        }
    }
}