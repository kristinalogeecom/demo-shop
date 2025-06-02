<?php

namespace DemoShop\Application\Configuration\Routes;

use DemoShop\Application\Presentation\Controller\AuthenticationController;
use DemoShop\Application\Presentation\Controller\CategoryController;
use DemoShop\Application\Presentation\Controller\DashboardController;
use DemoShop\Application\Presentation\Controller\ProductController;
use DemoShop\Infrastructure\Middleware\AdminAuthMiddleware;
use DemoShop\Infrastructure\Middleware\PasswordPolicyMiddleware;
use DemoShop\Infrastructure\Middleware\RedirectIfAuthenticatedMiddleware;
use DemoShop\Infrastructure\Router\Route;
use DemoShop\Infrastructure\Router\RouteDispatcher;

/**
 * Registers all web routes for the application.
 *
 * Defines mappings between HTTP methods and URL patterns
 * to controller actions, with optional middleware.
 */
class WebRouteRegistrar
{
    /**
     * Registers all web application routes to the provided dispatcher.
     *
     * @param RouteDispatcher $dispatcher
     *
     * @return void
     */
    public static function register(RouteDispatcher $dispatcher): void
    {
        $dispatcher->register('GET', new Route('/', [AuthenticationController::class, 'visitorPage']));
        $dispatcher->register('GET', new Route('/admin/login', [AuthenticationController::class, 'showLoginPage'], [RedirectIfAuthenticatedMiddleware::class]));
        $dispatcher->register('POST', new Route('/admin/login', [AuthenticationController::class, 'login'], [PasswordPolicyMiddleware::class]));
        $dispatcher->register('POST', new Route('/admin/logout', [AuthenticationController::class, 'logout']));


        $dispatcher->register('GET', new Route('/admin/dashboard', [DashboardController::class, 'layoutPage'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/dashboard/view', [DashboardController::class, 'dashboardPage'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/dashboard/data', [DashboardController::class, 'getDashboardStats'], [AdminAuthMiddleware::class]));

        $dispatcher->register('GET', new Route('/admin/products', [ProductController::class, 'getProductsHtml'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/products/data', [ProductController::class, 'getProducts'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/products/view/form', [ProductController::class, 'renderProductForm'], [AdminAuthMiddleware::class]));
        $dispatcher->register('POST', new Route('/admin/products/save', [ProductController::class, 'saveProduct'], [AdminAuthMiddleware::class]));
        $dispatcher->register('DELETE', new Route('/admin/products/delete/{id}', [ProductController::class, 'deleteProduct'], [AdminAuthMiddleware::class]));
        $dispatcher->register('POST', new Route('/admin/products/delete-batch', [ProductController::class, 'deleteBatch'], [AdminAuthMiddleware::class]));
        $dispatcher->register('POST', new Route('/admin/products/update-enabled', [ProductController::class, 'updateEnabledStatus'], [AdminAuthMiddleware::class]));


        $dispatcher->register('GET', new Route('/admin/categories', [CategoryController::class, 'getCategories'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/categories/view', [CategoryController::class, 'categoriesPage'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/categories-flat', [CategoryController::class, 'getFlatCategories'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/categories/{id}', [CategoryController::class, 'getCategory'], [AdminAuthMiddleware::class]));
        $dispatcher->register('POST', new Route('/admin/categories/save', [CategoryController::class, 'saveCategory'], [AdminAuthMiddleware::class]));
        $dispatcher->register('POST', new Route('/admin/categories/delete', [CategoryController::class, 'deleteCategory'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/categories/view/details/{id}', [CategoryController::class, 'renderCategoryDetails'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/categories/{id}/descendants', [CategoryController::class, 'getDescendantIds'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/categories/view/form', [CategoryController::class, 'renderCategoryForm'], [AdminAuthMiddleware::class]));
        $dispatcher->register('GET', new Route('/admin/categories/view/empty', [CategoryController::class, 'renderEmptyPanel'], [AdminAuthMiddleware::class]));

    }
}