<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\BusinessLogic\Model\CategoryModel;
use DemoShop\Application\BusinessLogic\Service\AdminServiceInterface;
use DemoShop\Application\BusinessLogic\Service\DashboardServiceInterface;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\JsonResponse;
use DemoShop\Infrastructure\Response\Response;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\RedirectResponse;
use Exception;

/**
 * Controller responsible for handling authentication logic.
 */
class AdminController
{
    private AdminServiceInterface $adminService;
    private DashboardServiceInterface $dashboardService;

    public function __construct(AdminServiceInterface $adminService, DashboardServiceInterface $dashboardService)
    {
        $this->adminService = $adminService;
        $this->dashboardService = $dashboardService;
    }

    /**
     * Handles the login request.
     *
     * @param Request $request The HTTP request object containing form data
     * @return Response
     */
    public function login(Request $request): Response
    {
        $admin = new Admin(
            $request->input('username'),
            $request->input('password'),
            $request->input('remember_me') === 'on'
        );

        $success = $this->adminService->attemptLogin($admin, $request);

        if($success) {
            return new RedirectResponse('/admin/dashboard', 302);
        } else {
            return new HtmlResponse('Login', [
                'errors' => ['Invalid username or password.'],
                'username' => $admin->getUsername(),
            ]);
        }
    }

    public function getDashboardStats(): Response
    {
        return new JsonResponse(
            $this->dashboardService->getDashboardStats()
        );
    }

    public function getProducts(): Response
    {
        return new JsonResponse([
            ['id' => 1, 'name' => 'Product 1', 'price' => 10.99],
            ['id' => 2, 'name' => 'Product 2', 'price' => 20.50]
        ]);
    }

    public function getCategories(): Response
    {
        try {
            $categories = $this->dashboardService->getAllCategories();
            return new JsonResponse($categories);
        } catch (Exception $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }

    public function getCategory($id): Response
    {
        try {
            $category = $this->dashboardService->getCategoryById($id);
            return new JsonResponse($category);
        } catch (Exception $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }


    public function saveCategory(Request $request): Response
    {
        try {
            $category = CategoryModel::fromArray(
                $request->only(['id', 'parent_id', 'name', 'code', 'description'])
            );

            $saved = $this->dashboardService->saveCategory($category);

            return new JsonResponse([
                'success' => true,
                'category' => $saved->toArray()
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 400);
        }
    }

    public function deleteCategory(Request $request): Response
    {
        $id = $request->input('id');

        if(!$id) {
            return new JsonResponse(['error' => 'Category ID is required.'], 400);
        }

        try {
            $this->dashboardService->deleteCategory($id);
            return new JsonResponse(['success' => true]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function getFlatCategories(): Response
    {
        try {
            $categories = $this->dashboardService->getFlatCategories();
            return new JsonResponse($categories);
        } catch (Exception $e) {
            return new JsonResponse(['errors' => $e->getMessage()], 500);
        }
    }

}