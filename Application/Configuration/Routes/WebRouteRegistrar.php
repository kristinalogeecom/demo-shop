<?php

namespace DemoShop\Application\Configuration\Routes;

use DemoShop\Application\BusinessLogic\Service\AdminServiceInterface;
use DemoShop\Application\Presentation\Controller\AdminController;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Middleware\AdminAuthMiddleware;
use DemoShop\Infrastructure\Middleware\PasswordPolicyMiddleware;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Response\RedirectResponse;
use DemoShop\Infrastructure\Router\Router;
use DemoShop\Infrastructure\Session\SessionManager;
use Exception;

/**
 * Defines and registers all web-facing routes for the application.
 *
 * Responsible for wiring routes to controller actions or inline closures,
 * and attaching appropriate middleware for route protection and validation.
 */

class WebRouteRegistrar
{
    /**
     *  Registers all application routes into the provided router instance.
     *
     * @param Router $router The application's router instance
     *
     * @return void
     */
    public static function register(Router $router): void
    {
        try {
            $controller = new AdminController(ServiceRegistry::get(AdminServiceInterface::class));

            /**
             * Public visitor homepage
             */
            $router->addRoute('GET', '/', function () {
                (new HtmlResponse('Visitor'))->send();
            });

            /**
             * Admin login form
             * Redirects to dashboard if admin is already logged in.
             */
            $router->addRoute('GET', '/admin/login', function () {
                $session = SessionManager::getInstance();

                if ($session->get('admin_logged_in')) {
                    (new RedirectResponse('/admin/dashboard'))->send();

                    return;
                }

                (new HtmlResponse('Login', ['errors' => [], 'username' => '']))->send();
            });

            /**
             * Admin login form submission
             * Runs password policy and authentication middleware before executing login.
             */
            $router->addRoute('POST', '/admin/login', function () use ($controller) {
                $request = ServiceRegistry::get(Request::class);

                try {
                    $middlewareChain = new PasswordPolicyMiddleware();
                    $middlewareChain->linkWith(new AdminAuthMiddleware());
                    $middlewareChain->check($request);

                    $response = $controller->login($request);
                    $response->send();
                } catch (Exception $e) {
                    (new HtmlResponse('Login', [
                        'errors' => explode("\n", $e->getMessage()),
                        'username' => $request->input('username'),
                    ]))->send();
                }
            });

            /**
             * Error pages
             */
            $router->addRoute('GET', '/404', function () {
                (new HtmlResponse('Error404'))->send();
            });

            $router->addRoute('GET', '/505', function () {
                (new HtmlResponse('Error505'))->send();
            });

            /**
             * Admin dashboard
             * Protected by authentication middleware.
             */
            $router->addRoute('GET', '/admin/dashboard', function () {
                $request = ServiceRegistry::get(Request::class);

                try {
                    $middlewareChain = new AdminAuthMiddleware();
                    $middlewareChain->check($request);

                    (new HtmlResponse('AdminDashboard'))->send();
                } catch (Exception $e) {
                    (new HtmlResponse('Login', [
                        'errors' => [$e->getMessage()],
                        'username' => ''
                    ]))->send();
                }
            });

        } catch (Exception $e) {
            error_log('Route registration error: ' . $e->getMessage());
        }
    }
}
