<?php

namespace DemoShop\Application\Configuration\Routes;

use DemoShop\Application\BusinessLogic\Service\AdminServiceInterface;
use DemoShop\Application\BusinessLogic\Service\DashboardServiceInterface;
use DemoShop\Application\Presentation\Controller\AdminController;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Middleware\AdminAuthMiddleware;
use DemoShop\Infrastructure\Middleware\PasswordPolicyMiddleware;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Response\JsonResponse;
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
            $controller = new AdminController(ServiceRegistry::get(AdminServiceInterface::class),
            ServiceRegistry::get(DashboardServiceInterface::class));

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
            $router->addRoute('GET', '/admin/dashboard', function () use ($controller) {
                $request = ServiceRegistry::get(Request::class);

                try {
                    $middlewareChain = new AdminAuthMiddleware();
                    $middlewareChain->check($request);

                    (new HtmlResponse('AdminDashboard'))->send();
                } catch (Exception $e) {
                    (new HtmlResponse('Login', ['errors' => ['Unauthorized. Please login first'], 'username' => '']))->send();
                }
            });

            $router->addRoute('GET', '/admin/dashboard/data', function () use ($controller) {
                $request = ServiceRegistry::get(Request::class);

                try {
                    $middlewareChain = new AdminAuthMiddleware();
                    $middlewareChain->check($request);

                    $response = $controller->getDashboardStats();
                    $response->send();
                } catch (Exception $e) {
                    (new JsonResponse(['error' => $e->getMessage()], 401))->send();
                }
            });

            /**
             * Product Management Routes
             */

            $router->addRoute('GET', '/admin/products', function() use ($controller) {
                try {
                    $middlewareChain = new AdminAuthMiddleware();
                    $middlewareChain->check(ServiceRegistry::get(Request::class));

                    $response = $controller->getProducts();
                    $response->send();
                } catch (Exception $e) {
                    (new JsonResponse(['error' => $e->getMessage()], 401))->send();
                }
            });

            /**
             * Category Management Routes
             */

            // Get all categories (hierarchical)
            $router->addRoute('GET', '/admin/categories', function() use ($controller) {
                try {
                    $middlewareChain = new AdminAuthMiddleware();
                    $middlewareChain->check(ServiceRegistry::get(Request::class));

                    $response = $controller->getCategories();
                    $response->send();
                } catch (Exception $e) {
                    (new JsonResponse(['error' => $e->getMessage()], 401))->send();
                }
            });

            // Get single category details
            $router->addRoute('GET', '/admin/categories/{id}', function() use ($controller) {
                try {
                    $request = ServiceRegistry::get(Request::class);

                    $middlewareChain = new AdminAuthMiddleware();
                    $middlewareChain->check($request);

                    $id = $request->param('id');

                    $response = $controller->getCategory($id);
                    $response->send();
                } catch (Exception $e) {
                    (new JsonResponse(['error' => $e->getMessage()], 401))->send();
                }
            });

            // Save category (create/update)
            $router->addRoute('POST', '/admin/categories/save', function() use ($controller) {
                try {
                    $middlewareChain = new AdminAuthMiddleware();
                    $middlewareChain->check(ServiceRegistry::get(Request::class));
                    $request = ServiceRegistry::get(Request::class);

                    $response = $controller->saveCategory($request);
                    $response->send();
                } catch (Exception $e) {
                    (new JsonResponse(['error' => $e->getMessage()], 401))->send();
                }
            });

            // Delete category
            $router->addRoute('POST', '/admin/categories/delete', function() use ($controller) {
                try {
                    $middlewareChain = new AdminAuthMiddleware();
                    $middlewareChain->check(ServiceRegistry::get(Request::class));

                    $request = ServiceRegistry::get(Request::class);
                    $response = $controller->deleteCategory($request);
                    $response->send();
                } catch (Exception $e) {
                    (new JsonResponse(['error' => $e->getMessage()], 401))->send();
                }
            });


        } catch (Exception $e) {
            error_log('Route registration error: ' . $e->getMessage());
        }
    }
}
