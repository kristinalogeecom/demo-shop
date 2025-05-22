<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\Persistence\Repository\AdminRepository;

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
     * Authenticates the admin and sets session if valid.
     *
     * @param Admin $admin
     *
     * @return bool
     */
    public function attemptLogin(Admin $admin): bool
    {
        $adminFromDb = $this->adminRepository->findByUsername($admin->getUsername());

        if ($adminFromDb !== null &&
            $this->adminRepository->verifyPassword($adminFromDb, $admin->getPassword())) {

            session_start();
            $_SESSION['admin_logged_in'] = true;

            return true;
        }

        return false;
    }
}