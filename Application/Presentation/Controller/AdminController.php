<?php

namespace DemoShop\Application\Presentation\Controller;

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
        $username = $request->input('username');
        $password = $request->input('password');

        $errors = $this->adminService->validateLogin($username, $password);

        if (!empty($errors)) {
            (new HtmlResponse('Login', [
                'errors' => $errors,
                'username' => $username
            ]))->send();

            return;
        }

        if($this->adminService->attemptLogin($username, $password)) {
            session_start();
            $_SESSION['admin_logged_in'] = true;
            (new RedirectResponse('/admin/dashboard', 302))->send();
        } else {
            (new HtmlResponse('Login', [
                'errors' => ['Invalid username or password.'],
                'username' => $username
            ]))->send();
        }

    }
}