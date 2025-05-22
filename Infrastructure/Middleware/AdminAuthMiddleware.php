<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Infrastructure\Http\Request;
use Exception;

class AdminAuthMiddleware extends Middleware
{

    /**
     * @throws Exception
     */
    protected function handle(Request $request): void
    {
        session_start();

        if(empty($_SESSION['admin_logged_in'])) {
            throw new Exception('Unauthorized: Admin access required.');
        }
    }
}