<?php
namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\BusinessLogic\ServiceInterface\AuthenticationServiceInterface;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\RedirectResponse;
use DemoShop\Infrastructure\Response\Response;

/**
 * Handles authentication-related HTTP actions for admin users,
 * including login and logout endpoints.
 */
class AuthenticationController
{
    private AuthenticationServiceInterface $authenticationService;

    public function __construct(AuthenticationServiceInterface $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * Handles admin login request.
     *
     * @param Request $request The incoming HTTP request with credentials.
     *
     * @return Response Redirects to dashboard if successful; otherwise, returns login view with errors.
     */
    public function login(Request $request): Response
    {
        $admin = new Admin(
            $request->input('username'),
            $request->input('password'),
            $request->input('remember_me') === 'on'
        );

        $success = $this->authenticationService->attemptLogin($admin, $request);

        return $success
            ? new RedirectResponse('/admin/dashboard')
            : new HtmlResponse('Login', [
                'errors' => ['Invalid username or password.'],
                'username' => $admin->getUsername()
            ]);
    }

    /**
     * Handles admin logout request.
     *
     * @param Request $request The current HTTP request.
     *
     * @return Response Redirects to login page after logout.
     */
    public function logout(Request $request): Response
    {
        $this->authenticationService->logout($request);
        return new RedirectResponse('/admin/login');
    }
}
