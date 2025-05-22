<?php

namespace DemoShop\Application\Presentation\Controller;

use DemoShop\Application\BusinessLogic\Model\Admin;
use DemoShop\Application\BusinessLogic\Service\AdminServiceInterface;
use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Response\Response;
use DemoShop\Infrastructure\Response\HtmlResponse;
use DemoShop\Infrastructure\Response\RedirectResponse;

/**
 * Controller responsible for handling authentication logic.
 */
class AdminController
{
    private AdminServiceInterface $adminService;

    public function __construct(AdminServiceInterface $adminService)
    {
        $this->adminService = $adminService;
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
}