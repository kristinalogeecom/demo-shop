<?php

namespace DemoShop\Application\Configuration\Routes;

use DemoShop\Application\BusinessLogic\Service\AdminServiceInterface;
use DemoShop\Application\BusinessLogic\Service\DashboardServiceInterface;
use DemoShop\Application\Presentation\Controller\AdminController;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Middleware\AdminAuthMiddleware;
use DemoShop\Infrastructure\Middleware\PasswordPolicyMiddleware;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\RedirectResponse;
use DemoShop\Infrastructure\Router\Router;
use Exception;

class WebRouteRegistrar
{
    public static function register(Router $router): void
    {
        try {
            $controller = new AdminController(
                ServiceRegistry::get(AdminServiceInterface::class),
                ServiceRegistry::get(DashboardServiceInterface::class)
            );

            self::registerPublicRoutes($router);
            self::registerAuthRoutes($router, $controller);
            self::registerAdminRoutes($router, $controller);

        } catch (Exception $e) {
            error_log('Route registration error: ' . $e->getMessage());
        }
    }

    private static function registerPublicRoutes(Router $router): void
    {
        $router->addRoute('GET', '/', fn() => (new HtmlResponse('Visitor'))->send());

        $router->addRoute('GET', '/admin/login', function () {
            $request = ServiceRegistry::get(Request::class);
            try {
                (new AdminAuthMiddleware())->check($request);
                (new RedirectResponse('/admin/dashboard'))->send();
            } catch (Exception $e) {
                (new HtmlResponse('Login', ['errors' => [], 'username' => '']))->send();
            }
        });

        $router->addRoute('POST', '/admin/login', function () {
            $request = ServiceRegistry::get(Request::class);
            try {
                (new PasswordPolicyMiddleware())->check($request);
                ServiceRegistry::get(AdminController::class)->login($request)->send();
            } catch (Exception $e) {
                (new HtmlResponse('Login', [
                    'errors' => explode("\n", $e->getMessage()),
                    'username' => $request->input('username')
                ]))->send();
            }
        });

        $router->addRoute('POST', '/admin/logout', function () {
            $request = ServiceRegistry::get(Request::class);
            ServiceRegistry::get(AdminController::class)->logout($request)->send();
        });

        $router->addRoute('GET', '/404', fn() => (new HtmlResponse('Error404'))->send());
        $router->addRoute('GET', '/505', fn() => (new HtmlResponse('Error505'))->send());
    }

    private static function registerAuthRoutes(Router $router, AdminController $controller): void
    {
        $router->addRoute('GET', '/admin/dashboard', function () use ($controller) {
            self::secure(fn() => (new HtmlResponse('AdminDashboard'))->send());
        });

        $router->addRoute('GET', '/admin/dashboard/data', function () use ($controller) {
            self::secure(fn() => $controller->getDashboardStats()->send());
        });
    }

    private static function registerAdminRoutes(Router $router, AdminController $controller): void
    {
        $router->addRoute('GET', '/admin/products', function () use ($controller) {
            self::secure(fn() => $controller->getProducts()->send());
        });

        $router->addRoute('GET', '/admin/categories', function () use ($controller) {
            self::secure(fn() => $controller->getCategories()->send());
        });

        $router->addRoute('GET', '/admin/categories-flat', function () use ($controller) {
            self::secure(fn() => $controller->getFlatCategories()->send());
        });

        $router->addRoute('GET', '/admin/categories/{id}', function () use ($controller) {
            self::secure(function () use ($controller) {
                $id = ServiceRegistry::get(Request::class)->param('id');
                $controller->getCategory($id)->send();
            });
        });

        $router->addRoute('POST', '/admin/categories/save', function () use ($controller) {
            self::secure(function () use ($controller) {
                $request = ServiceRegistry::get(Request::class);
                $controller->saveCategory($request)->send();
            });
        });

        $router->addRoute('POST', '/admin/categories/delete', function () use ($controller) {
            self::secure(function () use ($controller) {
                $request = ServiceRegistry::get(Request::class);
                $controller->deleteCategory($request)->send();
            });
        });
    }

    private static function secure(callable $callback): void
    {
        try {
            $middleware = new AdminAuthMiddleware();
            $middleware->check(ServiceRegistry::get(Request::class));
            $callback();
        } catch (Exception $e) {
            (new HtmlResponse('Login', ['errors' => ['Unauthorized. Please login first'], 'username' => '']))->send();
        }
    }
}
