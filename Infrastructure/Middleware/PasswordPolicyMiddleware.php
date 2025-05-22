<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Application\BusinessLogic\Service\AdminServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use Exception;


/**
 * Middleware that validates the submitted admin password.
 *
 * Applies password policy rules (length, complexity) before allowing request to proceed.
 * If the password is invalid, an exception is thrown and the chain is interrupted.
 */
class PasswordPolicyMiddleware extends Middleware
{

    /**
     * Validates the password from the incoming request against the policy.
     *
     * Retrieves the password from request input, delegates validation to AdminService,
     * and throws an exception if any validation rule fails.
     *
     * @param Request $request
     *
     * @return void
     *
     * @throws Exception If password does not meet the required policy.
     */
    public function handle(Request $request): void
    {
        $adminService = ServiceRegistry::get(AdminServiceInterface::class);

        $error = $adminService->validatePassword($request->input('password'));

        if ($error !== null) {
            throw new Exception($error);
        }
    }
}