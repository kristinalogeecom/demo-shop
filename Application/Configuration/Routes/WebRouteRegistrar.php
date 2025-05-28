<?php

namespace DemoShop\Application\Configuration\Routes;

use DemoShop\Application\Presentation\Controller\AuthenticationController;
use DemoShop\Application\Presentation\Controller\CategoryController;
use DemoShop\Application\Presentation\Controller\DashboardController;
use DemoShop\Application\Presentation\Controller\ProductController;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Middleware\AdminAuthMiddleware;
use DemoShop\Infrastructure\Middleware\PasswordPolicyMiddleware;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\RedirectResponse;
use DemoShop\Infrastructure\Router\Router;
use Exception;

/**
 * Registers all web routes for the application,
 * including public, dashboard, product, and category endpoints.
 */
class WebRouteRegistrar
{
    /**
     * Registers all routes using the given router instance.
     *
     * @param Router $router The application's router.
     *
     * @return void
     */
    public static function register(Router $router): void
    {
        try {
            self::registerPublicRoutes($router);
            self::registerDashboardRoutes($router);
            self::registerCategoryRoutes($router);
            self::registerProductRoutes($router);
        } catch (Exception $e) {
            error_log('Route registration error: ' . $e->getMessage());
        }
    }

    /**
     * Registers public routes such as login, logout, and error pages.
     *
     * @param Router $router The router instance.
     *
     * @return void
     *
     * @throws Exception
     */
    private static function registerPublicRoutes(Router $router): void
    {
        $authController = ServiceRegistry::get(AuthenticationController::class);

        $router->addRoute('GET', '/', fn() => (new HtmlResponse('Visitor'))->send());

        $router->addRoute('GET', '/admin/login', function () {
            $request = ServiceRegistry::get(Request::class);
            try {
                (new AdminAuthMiddleware())->check($request);
                (new RedirectResponse('/admin/dashboard'))->send();
            } catch (Exception) {
                (new HtmlResponse('Login', ['errors' => [], 'username' => '']))->send();
            }
        });

        $router->addRoute('POST', '/admin/login', function () use ($authController) {
            $request = ServiceRegistry::get(Request::class);
            try {
                (new PasswordPolicyMiddleware())->check($request);
                $authController->login($request)->send();
            } catch (Exception $e) {
                (new HtmlResponse('Login', [
                    'errors' => explode("\n", $e->getMessage()),
                    'username' => $request->input('username')
                ]))->send();
            }
        });

        $router->addRoute('POST', '/admin/logout', function () use ($authController) {
            $request = ServiceRegistry::get(Request::class);
            $authController->logout($request)->send();
        });

        $router->addRoute('GET', '/404', fn() => (new HtmlResponse('Error404'))->send());
        $router->addRoute('GET', '/505', fn() => (new HtmlResponse('Error505'))->send());
    }

    /**
     * Registers routes related to the admin dashboard.
     *
     * @param Router $router The router instance.
     *
     * @return void
     *
     * @throws Exception
     */
    private static function registerDashboardRoutes(Router $router): void
    {
        $dashboardController = ServiceRegistry::get(DashboardController::class);

        $router->addRoute('GET', '/admin/dashboard', function () {
            self::secure(fn() => (new HtmlResponse('AdminDashboard'))->send());
        });

        $router->addRoute('GET', '/admin/dashboard/data', function () use ($dashboardController) {
            self::secure(fn() => $dashboardController->getDashboardStats()->send());
        });
    }

    /**
     * Registers routes related to product management.
     *
     * @param Router $router The router instance.
     *
     * @return void
     *
     * @throws Exception
     */
    private static function registerProductRoutes(Router $router): void
    {
        $productController = ServiceRegistry::get(ProductController::class);

        $router->addRoute('GET', '/admin/products', function () use ($productController) {
            self::secure(fn() => $productController->getProducts()->send());
        });
    }


    /**
     * Registers routes related to category management.
     *
     * @param Router $router The router instance.
     *
     * @return void
     *
     * @throws Exception
     */
    private static function registerCategoryRoutes(Router $router): void
    {
        $categoryController = ServiceRegistry::get(CategoryController::class);

        $router->addRoute('GET', '/admin/categories', function () use ($categoryController) {
            self::secure(fn() => $categoryController->getCategories()->send());
        });

        $router->addRoute('GET', '/admin/categories-flat', function () use ($categoryController) {
            self::secure(fn() => $categoryController->getFlatCategories()->send());
        });

        $router->addRoute('GET', '/admin/categories/{id}', function () use ($categoryController) {
            self::secure(function () use ($categoryController) {
                $id = ServiceRegistry::get(Request::class)->param('id');
                $categoryController->getCategory($id)->send();
            });
        });

        $router->addRoute('POST', '/admin/categories/save', function () use ($categoryController) {
            self::secure(function () use ($categoryController) {
                $request = ServiceRegistry::get(Request::class);
                $categoryController->saveCategory($request)->send();
            });
        });

        $router->addRoute('POST', '/admin/categories/delete', function () use ($categoryController) {
            self::secure(function () use ($categoryController) {
                $request = ServiceRegistry::get(Request::class);
                $categoryController->deleteCategory($request)->send();
            });
        });

        $router->addRoute('GET', '/admin/categories/{id}/descendants', function () use ($categoryController) {
            self::secure(function () use ($categoryController) {
                $id = ServiceRegistry::get(Request::class)->param('id');
                $categoryController->getDescendantIds($id)->send();
            });
        });

    }


    /**
     * Executes a route callback only if the admin is authenticated.
     *
     * @param callable $callback The route handler to execute if authenticated.
     *
     * @return void
     */
    private static function secure(callable $callback): void
    {
        try {
            $middleware = new AdminAuthMiddleware();
            $middleware->check(ServiceRegistry::get(Request::class));
            $callback();
        } catch (Exception) {
            (new HtmlResponse('Login', ['errors' => ['Unauthorized. Please login first'], 'username' => '']))->send();
        }
    }
}
