<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Infrastructure\Http\Request;

interface AdminServiceInterface
{
    /**
     * Authenticates the admin and sets session if valid.
     *
     * @param Admin $admin
     * @param Request $request
     * @return bool
     */
    public function attemptLogin(Admin $admin, Request $request): bool;

    /**
     * Validates password against defined security rules.
     *
     * @param string|null $password
     * @return ?string
     */
    public function validatePassword(?string $password): ?string;

    public function logout(Request $request): void;
}