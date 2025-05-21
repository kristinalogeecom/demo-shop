<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\Persistence\Repository\AdminRepository;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\RedirectResponse;

/**
 * Handles business logic related to admin authentication,
 * including login validation and credential verification.
 */
class AdminService
{
    private AdminRepository $adminRepository;

    /**
     * @param AdminRepository $adminRepository
     */
    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    /**
     * Attempts to log in an admin using provided credentials.
     *
     * @param Admin $admin
     *
     * @return HtmlResponse|RedirectResponse
     */
    public function handleLogin(Admin $admin):  HtmlResponse|RedirectResponse
    {
        $errors = $this->validateLogin($admin);


        if (!empty($errors)) {
            return new HtmlResponse('Login', [
                'errors' => $errors,
                'username' => $admin->getUsername()
            ]);
        }

        if ($this->attemptLogin($admin)) {
            session_start();
            $_SESSION['admin_logged_in'] = true;

            return new RedirectResponse('/admin/dashboard', 302);
        }

        return new HtmlResponse('Login', [
            'errors' => ['Invalid username or password.'],
            'username' => $admin->getUsername()
        ]);
    }

    /**
     * Validates login form input and checks password complexity requirements.
     *
     * @param Admin $admin
     *
     * @return array An array of validation error messages (empty if no errors).
     */
    public function validateLogin(Admin $admin): array
    {
        $errors = [];

        if (empty($admin->getUsername())) {
            $errors[] = "Username is required.";
        }

        $password = $admin->getPassword();

        if (empty($password)) {
            $errors[] = "Password is required.";
        } else {
            $valid = true;

            if (strlen($password) < 8 ||
                !preg_match('/[A-Z]/', $password) ||
                !preg_match('/[a-z]/', $password) ||
                !preg_match('/[0-9]/', $password) ||
                !preg_match('/[^a-zA-Z0-9]/', $password)) {
                $valid = false;
            }

            if(!$valid) {
                $errors[] = "Password must be at least 8 characters long 
                and include uppercase, lowercase, number, and special character.";
            }
        }

        return $errors;
    }

    private function attemptLogin(Admin $admin): bool
    {
        $adminFromDb = $this->adminRepository->findByUsername($admin->getUsername());

        return $adminFromDb !== null &&
            $this->adminRepository->verifyPassword($adminFromDb, $admin->getPassword());
    }
}