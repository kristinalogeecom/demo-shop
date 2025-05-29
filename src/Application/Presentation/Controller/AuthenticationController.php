<?php
namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\BusinessLogic\ServiceInterface\AuthenticationServiceInterface;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Exception\DecryptionException;
use DemoShop\Infrastructure\Exception\EncryptionException;
use DemoShop\Infrastructure\Exception\ServiceNotFoundException;
use DemoShop\Infrastructure\Exception\ValidationException;
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
            $request->has('remember_me')
        );

        try{
            $success = $this->getAuthenticationService()->attemptLogin($admin, $request);
        } catch (DecryptionException) {
            return new HtmlResponse('Login', [
                'errors' => ['Invalid encrypted data.'],
                'username' => $admin->getUsername(),
            ]);
        } catch (EncryptionException) {
            return new HtmlResponse('Login', [
                'errors' => ['Failed to encrypt data.'],
                'username' => $admin->getUsername(),
            ]);
        } catch (ValidationException $e) {
            return new HtmlResponse('Login', [
                'errors' => [$e->getMessage()],
                'username' => $admin->getUsername(),
            ]);
        } catch (Exception $e){
            return new HtmlResponse('Login', [
                'errors' => ['Unexpected error occurred.'],
                'username' => $admin->getUsername(),
            ]);
        }

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
     *
     * @throws ServiceNotFoundException
     */
    public function logout(Request $request): Response
    {
        $this->getAuthenticationService()->logout($request);
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
        $errorMessage = null;

        if ($request->query('error') === 'unauthorized') {
            $errorMessage = 'Unauthorized. Please log in first.';
        }

        return new HtmlResponse('Login', [
            'errors' => $errorMessage ? [$errorMessage] : [],
            'username' => ''
        ]);
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
     * @return AuthenticationServiceInterface
     *
     * @throws ServiceNotFoundException
     */
    private function getAuthenticationService(): AuthenticationServiceInterface
    {
        return ServiceRegistry::get(AuthenticationServiceInterface::class);
    }

}
