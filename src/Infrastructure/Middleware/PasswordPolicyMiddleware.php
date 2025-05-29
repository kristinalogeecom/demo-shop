<?php

namespace DemoShop\Infrastructure\Middleware;

use DemoShop\Application\BusinessLogic\ServiceInterface\AuthenticationServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\ResponseException;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;
use DemoShop\Infrastructure\Exception\ValidationException;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\HtmlResponse;

/**
 * Middleware that validates the submitted admin password.
 *
 * Applies password policy rules (length, complexity) before allowing request to proceed.
 * If the password is invalid, an exception is thrown and the chain is interrupted.
 */
class PasswordPolicyMiddleware extends Middleware
{
    /**
     *  Validates the password from the incoming request against the policy.
     *
     *  Retrieves the password from request input, delegates validation to AdminService,
     *  and throws an exception if any validation rule fails.
     *
     * @param Request $request
     *
     * @return void
     *
     * @throws ResponseException
     * @throws ServiceNotFoundException
     */
    public function handle(Request $request): void
    {
        $adminService = ServiceRegistry::get(AuthenticationServiceInterface::class);
        try {
            $adminService->validatePassword($request->input('password'));
        } catch (ValidationException $e) {
            throw new ResponseException(
                new HtmlResponse('Login', [
                    'errors' => $e->getErrors(),
                    'username' => $request->input('username'),
                ])
            );
        }
    }
}