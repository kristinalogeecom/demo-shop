<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\BusinessLogic\Service\AdminService;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\RedirectResponse;

/**
 * Controller responsible for handling authentication logic.
 */
class AdminController
{
    private AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    /**
     * Displays the admin login form.
     *
     * @return void
     */
    public function showLogin(): void
    {
        (new HtmlResponse('Login', ['errors' => [], 'username' => '']))->send();
    }

    /**
     * Handles the login request.
     *
     * @param Request $request The HTTP request object containing form data
     * @return void
     */
    public function login(Request $request): void
    {
        $admin = new Admin(
            $request->input('username'),
            $request->input('password'),
        );

        $response = $this->adminService->handleLogin($admin);

        $response->send();

    }
}