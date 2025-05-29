<?php

namespace DemoShop\Application\BusinessLogic\Service;

use DateTime;
use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\BusinessLogic\RepositoryInterface\AdminTokenRepositoryInterface;
use DemoShop\Application\BusinessLogic\RepositoryInterface\AuthenticationRepositoryInterface;
use DemoShop\Application\BusinessLogic\ServiceInterface\AuthenticationServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;
use DemoShop\Infrastructure\Exception\ValidationException;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Security\CookieManager;
use Exception;

/**
 * Handles business logic related to admin authentication,
 * including login validation, token-based login and logout handling.
 */
class AuthenticationService implements AuthenticationServiceInterface
{
    private AuthenticationRepositoryInterface $authenticationRepository;
    private AdminTokenRepositoryInterface $adminTokenRepository;
    private CookieManager $cookieManager;


    /**
     * @throws ServiceNotFoundException
     */
    public function __construct()
    {
        $this->authenticationRepository = ServiceRegistry::get(AuthenticationRepositoryInterface::class);
        $this->adminTokenRepository = ServiceRegistry::get(AdminTokenRepositoryInterface::class);
        $this->cookieManager = ServiceRegistry::get(CookieManager::class);
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

        if (!$adminFromDb) {
            return false;
        }

        if (!$this->authenticationRepository->verifyPassword($adminFromDb, $admin->getPassword())) {
            return false;
        }

        $expires = $admin->isRememberMe()
            ? new DateTime('+30 days')
            : new DateTime('+30 minutes');

        if ($admin->isRememberMe()) {
            $token = bin2hex(random_bytes(32));
            $this->cookieManager->setPersistentToken('admin_token', $token, $expires);
            $this->adminTokenRepository->storeToken($adminFromDb->id, $token, $expires);
        } else {
            $this->cookieManager->setEncryptedSession('admin_session', [
                'admin_id' => $adminFromDb->id,
                'exp' => $expires->getTimestamp(),
            ], $expires);
        }

        return true;
    }

    /**
     *  Validates the strength of a given password.
     *
     * @param string|null $password
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function validatePassword(?string $password): void
    {
        if (
            empty($password) ||
            strlen($password) < 8 ||
            !preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password) ||
            !preg_match('/[^a-zA-Z0-9]/', $password)
        ) {
            throw new ValidationException(
                ['Password must be at least 8 characters long and contain 
            at least one uppercase letter, one lowercase letter, one number, and one special character.']
            );
        }
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
        $token = $this->cookieManager->getCookie('admin_token');

        if($token) {
            $this->adminTokenRepository->deleteToken($token);
            $this->cookieManager->clearCookie('admin_token');
        }

        $this->cookieManager->clearCookie('admin_session');
    }

}