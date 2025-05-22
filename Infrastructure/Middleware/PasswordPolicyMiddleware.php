<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Infrastructure\Http\Request;
use Exception;

class PasswordPolicyMiddleware extends Middleware
{
    /**
     * @throws Exception
     */
    public function handle(Request $request): void
    {
        $password = $request->input('password');

        if(empty($password) ||
            strlen($password) < 8 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^a-zA-Z0-9]/', $password)
        ) {
            throw new Exception('Password does not meet security requirements.');
        }
    }
}