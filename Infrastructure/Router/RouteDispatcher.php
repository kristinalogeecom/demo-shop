<?php

namespace DemoShop\Infrastructure\Router;

use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Response\Response;
use Exception;

/**
 * Responsible for dispatching incoming HTTP requests to the appropriate route.
 *
 * Matches the request URL and method against registered routes,
 * executes any assigned middleware, and calls the associated controller action.
 */
class RouteDispatcher
{
    private Request $request;
    private array $routes = [];

    /**
     * Initializes the dispatcher with the current HTTP request.
     *
     * @param Request $request The current HTTP request instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Registers a route under a specific HTTP method.
     *
     * Routes are grouped by method (GET, POST, etc.).
     *
     * @param string $method The HTTP method (e.g. 'GET', 'POST').
     * @param Route  $route  The route instance to register.
     *
     * @return void
     */
    public function register(string $method, Route $route): void
    {
        $this->routes[$method][] = $route;
    }

    /**
     * Dispatches the current request to the first matching route.
     *
     * Workflow:
     * - Determines request method and URL.
     * - Finds the first route that matches the URL.
     * - Executes all middleware associated with that route.
     * - Calls the route's controller method.
     * - Sends the response to the client.
     * - Redirects to /404 if no route matches.
     *
     * @throws Exception If middleware or controller execution fails.
     *
     * @return void
     */
    public function dispatch(): void
    {
        $method = $this->request->method();
        $url = rtrim($this->request->url(), '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $route) {
            if ($route->matches($url)) {
                $this->request->setRouteParams($route->extractParams($url));

                foreach ($route->getMiddlewares() as $middlewareClass) {
                    $middleware = ServiceRegistry::get($middlewareClass);
                    $middleware->check($this->request);
                }

                $response = call_user_func($route->getCallable(), $this->request);
                if ($response instanceof Response) {
                    $response->send();
                }

                return;
            }
        }
        header("Location: /404");
        exit;
    }
}
