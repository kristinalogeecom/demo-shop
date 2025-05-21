<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\Persistence\Repository\AdminRepository;

class AdminService
{
    private AdminRepository $adminRepository;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function attemptLogin(string $username, string $password): bool
    {
        $admin = $this->adminRepository->findByUsername($username);

        return $admin && password_verify($password, $admin->password);
    }

    public function validateLogin(?string $username, ?string $password): array
    {
        $errors = [];

        if (empty($username)) {
            $errors[] = "Username is required.";
        }

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
}