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
 * including login and logout, error pages and the visitor landing page.
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
     * @throws Exception
     */
    public function logout(Request $request): Response
    {
        $this->authenticationService()->logout($request);
        return new RedirectResponse('/admin/login');
    }

    /**
     * Displays the admin login page.
     *
     * @param Request $request The current HTTP request.
     *
     * @return Response Rendered login HTML page.
     */
    public function showLoginPage(Request $request): Response
    {
        return new HtmlResponse('Login', [
            'errors' => [],
            'username' => ''
        ]);
    }

    /**
     * Displays the 404 Not Found error page.
     *
     * @param Request $request The current HTTP request.
     *
     * @return Response Rendered 404 error page.
     */
    public function error404(Request $request): Response
    {
        return new HtmlResponse('Error404');
    }

    /**
     * Displays the 505 Internal Server Error page.
     *
     * @param Request $request The current HTTP request.
     *
     * @return Response Rendered 505 error page.
     */
    public function error505(Request $request): Response
    {
        return new HtmlResponse('Error505');
    }

    /**
     * Displays the public-facing visitor landing page.
     *
     * @param Request $request The current HTTP request.
     *
     * @return Response Rendered visitor page.
     */
    public function visitorPage(Request $request): Response
    {
        return new HtmlResponse('Visitor');
    }

    /**
     * Retrieves the authentication service instance from the service container.
     *
     * @return AuthenticationServiceInterface The authentication service.
     *
     * @throws Exception If the service is not registered.
     */
    private function authenticationService(): AuthenticationServiceInterface
    {
        return ServiceRegistry::get(AuthenticationServiceInterface::class);
    }

}
