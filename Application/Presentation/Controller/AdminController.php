<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\BusinessLogic\Service\AdminServiceInterface;
use DemoShop\Application\BusinessLogic\Service\DashboardServiceInterface;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\JsonResponse;
use DemoShop\Infrastructure\Response\Response;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\RedirectResponse;
use Exception;
use function Symfony\Component\Translation\t;

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
        );

        $success = $this->adminService->attemptLogin($admin);

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
}