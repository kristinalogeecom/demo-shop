<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DateTime;
use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\BusinessLogic\ServiceInterface\AuthenticationServiceInterface;
use DemoShop\Application\Persistence\Repository\AuthenticationRepository;
use DemoShop\Application\Persistence\Repository\AdminTokenRepository;
use DemoShop\Infrastructure\Http\Request;
use Exception;

/**
 * Handles business logic related to admin authentication,
 * including login validation, token-based login and logout handling.
 */
class AuthenticationService implements AuthenticationServiceInterface
{
    private AuthenticationRepository $authenticationRepository;
    private AdminTokenRepository $adminTokenRepository;

    /**
     * @param AuthenticationRepository $authenticationRepository
     * @param AdminTokenRepository $adminTokenRepository
     */
    public function __construct(AuthenticationRepository $authenticationRepository, AdminTokenRepository $adminTokenRepository)
    {
        $this->authenticationRepository = $authenticationRepository;
        $this->adminTokenRepository = $adminTokenRepository;
    }

    /**
     * Attempts to log in the admin.
     * Supports both "remember me" (persistent) and short session logins.
     *
     * @param Admin $admin The admin model containing username and password.
     * @param Request $request The current HTTP request.
     *
     * @return bool True if login is successful, false otherwise.
     *
     * @throws Exception
     */
    public function attemptLogin(Admin $admin, Request $request): bool
    {
        $adminFromDb = $this->authenticationRepository->findByUsername($admin->getUsername());

        if ($adminFromDb !== null &&
            $this->authenticationRepository->verifyPassword($adminFromDb, $admin->getPassword())) {

            if ($admin->isRememberMe()) {
                $token = bin2hex(random_bytes(32));
                $expires = new DateTime('+30 days');
                $this->setAuthTokenCookie($token, $expires);
                $this->adminTokenRepository->storeToken($adminFromDb->id, $token, $expires);
            } else {
                $this->setShortSessionCookie();
            }

            return true;
        }

        return false;
    }

    /**
     *  Validates the strength of a given password.
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


    /**
     * Logs out the admin by clearing stored tokens and cookies.
     *
     * @param Request $request The current HTTP request.
     *
     * @return void
     */
    public function logout(Request $request): void
    {
        $token = $_COOKIE['admin_token'] ?? null;

        if($token) {
            $this->adminTokenRepository->deleteToken($token);
            $this->clearAuthTokenCookie();
        }

        $this->clearShortSessionCookie();
    }


    /**
     * Sets a persistent authentication token cookie (30 days).
     *
     * @param string $token The authentication token.
     * @param DateTime $expires The expiration time for the cookie.
     *
     * @return void
     */
    private function setAuthTokenCookie(string $token, DateTime $expires): void
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $isLocal = str_contains($host, 'localhost') || str_contains($host, '127.0.0.1');

        setcookie('admin_token', $token, [
            'expires' => $expires->getTimestamp(),
            'path' => '/',
            'secure' => !$isLocal,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Clears the persistent authentication token cookie.
     *
     * @return void
     */
    private function clearAuthTokenCookie(): void
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $isLocal = str_contains($host, 'localhost') || str_contains($host, '127.0.0.1');

        setcookie('admin_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => !$isLocal,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Sets a short-term session cookie (30 minutes).
     *
     * @return void
     */
    private function setShortSessionCookie(): void
    {
        $expires = new DateTime('+30 minutes');
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $isLocal = str_contains($host, 'localhost') || str_contains($host, '127.0.0.1');

        setcookie('admin_logged_in_cookie', 'true', [
            'expires' => $expires->getTimestamp(),
            'path' => '/',
            'secure' => !$isLocal,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Clears the short-term session cookie.
     *
     * @return void
     */
    private function clearShortSessionCookie(): void
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $isLocal = str_contains($host, 'localhost') || str_contains($host, '127.0.0.1');

        setcookie('admin_logged_in_cookie', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => !$isLocal,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}