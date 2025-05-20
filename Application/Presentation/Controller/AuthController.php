<?php

/**
 * Controller responsible for handling authentication logic.
 */
class AuthController
{
    /**
     * Displays the admin login form.
     *
     * @return void
     */
    public function showLogin(): void
    {
        include __DIR__ . '/../Page/Login.phtml';
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

        if($username === 'admin' && $password === 'secret') {
            echo "Login successful!";
        } else {
            echo "Invalid credentials.";
        }
    }
}