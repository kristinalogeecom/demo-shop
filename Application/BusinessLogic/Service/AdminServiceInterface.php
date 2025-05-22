<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\Model\Admin;

interface AdminServiceInterface
{
    /**
     * Authenticates the admin and sets session if valid.
     *
     * @param Admin $admin
     * @return bool
     */
    public function attemptLogin(Admin $admin): bool;

    /**
     * Validates password against defined security rules.
     *
     * @param string|null $password
     * @return ?string
     */
    public function validatePassword(?string $password): ?string;
}