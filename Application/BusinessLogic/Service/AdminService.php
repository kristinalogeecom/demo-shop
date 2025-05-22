<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\Persistence\Repository\AdminRepository;
use DemoShop\Infrastructure\Session\SessionManager;

/**
 * Handles business logic related to admin authentication,
 * including login validation and credential verification.
 */
class AdminService implements AdminServiceInterface
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
     * Authenticates the admin and sets session if valid.
     *
     * @param Admin $admin
     *
     * @return bool True if authentication is successful, false otherwise
     */
    public function attemptLogin(Admin $admin): bool
    {
        $adminFromDb = $this->adminRepository->findByUsername($admin->getUsername());

        if ($adminFromDb !== null &&
            $this->adminRepository->verifyPassword($adminFromDb, $admin->getPassword())) {

            $session = SessionManager::getInstance();
            $session->set('admin_logged_in', true);

            return true;
        }

        return false;
    }

    /**
     *  Validates the strength of a given password.
     *
     *  Password must meet all the following conditions:
     *  - Minimum 8 characters
     *  - At least one uppercase letter
     *  - At least one lowercase letter
     *  - At least one digit
     *  - At least one special character
     *
     * @param string|null $password
     *
     * @return string|null A validation error message if invalid; null if valid.
     */
    public function validatePassword(?string $password): ?string
    {
        if (
            empty($password) ||
            strlen($password) < 8 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^a-zA-Z0-9]/', $password)
        ) {
            return 'Password must be at least 8 characters long and contain 
            at least one uppercase letter, one lowercase letter, one number, and one special character.';
        }

        return null;
    }
    
}