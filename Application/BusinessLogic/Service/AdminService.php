<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\Persistence\Repository\AdminRepository;
use DemoShop\Application\Persistence\Repository\AdminTokenRepository;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Session\SessionManager;

/**
 * Handles business logic related to admin authentication,
 * including login validation and credential verification.
 */
class AdminService implements AdminServiceInterface
{
    private AdminRepository $adminRepository;
    private AdminTokenRepository $adminTokenRepository;

    /**
     * @param AdminRepository $adminRepository
     * @param AdminTokenRepository $adminTokenRepository
     */
    public function __construct(AdminRepository $adminRepository, AdminTokenRepository $adminTokenRepository)
    {
        $this->adminRepository = $adminRepository;
        $this->adminTokenRepository = $adminTokenRepository;
    }


    /**
     * Authenticates the admin and sets session and cookie if successful.
     */
    public function attemptLogin(Admin $admin, Request $request): bool
    {
        $adminFromDb = $this->adminRepository->findByUsername($admin->getUsername());

        if ($adminFromDb !== null &&
            $this->adminRepository->verifyPassword($adminFromDb, $admin->getPassword())) {

            $session = SessionManager::getInstance();
            $session->set('admin_logged_in', true);

            if ($request->input('remember_me')) {
                $token = bin2hex(random_bytes(32));
                $expires = new \DateTime('+30 days');

                setcookie('admin_token', $token, [
                    'expires' => $expires->getTimestamp(),
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);

                $this->adminTokenRepository->storeToken($adminFromDb->id, $token, $expires);
            }

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