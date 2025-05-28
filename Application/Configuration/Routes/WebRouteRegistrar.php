<?php

namespace DemoShop\Application\Configuration\Routes;

use DemoShop\Application\Presentation\Controller\AuthenticationController;
use DemoShop\Application\Presentation\Controller\CategoryController;
use DemoShop\Application\Presentation\Controller\DashboardController;
use DemoShop\Application\Presentation\Controller\ProductController;
use DemoShop\Infrastructure\Middleware\AdminAuthMiddleware;
use DemoShop\Infrastructure\Middleware\PasswordPolicyMiddleware;
use DemoShop\Infrastructure\Router\Route;
use DemoShop\Infrastructure\Router\RouteDispatcher;

class WebRouteRegistrar
{
    public static function register(RouteDispatcher $dispatcher): void
    {
        $authController = new AuthenticationController();
        $dashboardController = new DashboardController();
        $categoryController = new CategoryController();
        $productController = new ProductController();

        $dispatcher->register('GET', new Route('/', [$authController, 'visitorPage']));
        $dispatcher->register('GET', new Route('/admin/login', [$authController, 'showLoginPage']));
        $dispatcher->register('POST', new Route('/admin/login', [$authController, 'login'], [PasswordPolicyMiddleware::class]));
        $dispatcher->register('POST', new Route('/admin/logout', [$authController, 'logout']));

        $dispatcher->register('GET', new Route('/404', [$authController, 'error404']));
        $dispatcher->register('GET', new Route('/505', [$authController, 'error505']));

        $dispatcher->register('GET', new Route('/admin/dashboard', [$dashboardController, 'dashboardPage'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/dashboard/data', [$dashboardController, 'getDashboardStats'], [AdminAuthMiddleware::class]));

        $dispatcher->register('GET', new Route('/admin/products', [$productController, 'getProducts'], [AdminAuthMiddleware::class]));

        $dispatcher->register('GET', new Route('/admin/categories', [$categoryController, 'getCategories'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/categories-flat', [$categoryController, 'getFlatCategories'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/categories/{id}', [$categoryController, 'getCategory'], [AdminAuthMiddleware::class]));
        $dispatcher->register('POST', new Route('/admin/categories/save', [$categoryController, 'saveCategory'], [AdminAuthMiddleware::class]));
        $dispatcher->register('POST', new Route('/admin/categories/delete', [$categoryController, 'deleteCategory'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/categories/{id}/descendants', [$categoryController, 'getDescendantIds'], [AdminAuthMiddleware::class]));
    }
}