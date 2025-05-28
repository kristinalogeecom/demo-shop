<?php
namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\BusinessLogic\ServiceInterface\AuthenticationServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\RedirectResponse;
use DemoShop\Infrastructure\Response\Response;
use Exception;

/**
 * Handles authentication-related HTTP actions for admin users,
 * including login and logout endpoints.
 */
class AuthenticationController
{

    /**
     * Handles admin login request.
     *
     * @param Request $request The incoming HTTP request with credentials.
     *
     * @return Response Redirects to dashboard if successful; otherwise, returns login view with errors.
     * @throws Exception
     */
    public function login(Request $request): Response
    {
        $admin = new Admin(
            $request->input('username'),
            $request->input('password'),
            $request->input('remember_me') === 'on'
        );


        $success = $this->authenticationService()->attemptLogin($admin, $request);

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
     * @throws \Exception
     */
    public function logout(Request $request): Response
    {
        $this->authenticationService()->logout($request);
        return new RedirectResponse('/admin/login');
    }

    public function showLoginPage(Request $request): Response
    {
        return new HtmlResponse('Login', [
            'errors' => [],
            'username' => ''
        ]);
    }

    public function error404(Request $request): Response
    {
        return new HtmlResponse('Error404');
    }

    public function error505(Request $request): Response
    {
        return new HtmlResponse('Error505');
    }

    public function visitorPage(Request $request): Response
    {
        return new HtmlResponse('Visitor');
    }

    /**
     * @throws Exception
     */
    private function authenticationService(): AuthenticationServiceInterface
    {
        return ServiceRegistry::get(AuthenticationServiceInterface::class);
    }

}
