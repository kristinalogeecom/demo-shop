<?php

namespace DemoShop\Application\BusinessLogic\ServiceInterface;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Infrastructure\Http\Request;

/**
 * Interface for handling admin authentication logic,
 * including login, password validation, and logout.
 */
interface AuthenticationServiceInterface
{
    /**
     * Attempts to authenticate an admin based on credentials.
     * If successful, sets an authentication cookie (persistent or short-term).
     *
     * @param Admin $admin The admin model containing credentials.
     * @param Request $request The current HTTP request.
     *
     * @return bool True if login is successful, false otherwise.
     */
    public function attemptLogin(Admin $admin, Request $request): bool;

    /**
     * Validates password against defined security rules.
     *
     * @param string|null $password
     *
     * @return void
     */
    public function validatePassword(?string $password): void;

    /**
     * Logs out the admin by removing authentication cookies and token from storage.
     *
     * @param Request $request The current HTTP request.
     *
     * @return void
     */
    public function logout(Request $request): void;
}