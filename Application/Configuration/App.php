<?php

require_once __DIR__ . '/../../Infrastructure/Router/Router.php';
require_once __DIR__ . '/../../Infrastructure/Http/Request.php';

/**
 * The main application class responsible for bootstrapping
 * the router and handling the incoming HTTP request.
 */
class App
{
    protected Router $router;
    protected Request $request;

    /**
     * Initializes the router and request objects.
     */
    public function __construct()
    {
        $this->router = new Router();
        $this->request = new Request();
    }

    /**
     * Loads route definitions and dispatches the request to the appropriate handler.
     *
     * @return void
     */
    public function boot(): void
    {
        $router = $this->router;
        include __DIR__ . '/Routes/Web.php';
        $this->router->matchRoute($this->request);
    }
}