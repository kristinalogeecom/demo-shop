<?php

namespace DemoShop\Infrastructure\Router;

use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Http\Request;
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
     * Dispatches the current request to the appropriate controller action.
     *
     * Workflow:
     * - Determines request method and URL.
     * - Finds the first route that matches the URL.
     * - Executes all middleware associated with that route.
     * - Calls the route's controller method.
     * - Sends the response to the client.
     *
     * @throws Exception If middleware fails, controller class is missing,
     *                  controller method does not exist,
     *                  or no matching route is found.
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

                list($controllerClass, $method) = $route->getControllerAction();

                if (!class_exists($controllerClass)) {
                    throw new Exception("Controller class $controllerClass does not exist.");
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $method)) {
                    throw new Exception("Method $method does not exist in controller $controllerClass.");
                }

                $response = $controller->$method($this->request);

                if ($response instanceof Response) {
                    $response->send();
                }

                return;
            }
        }

        // THROW EXCEPTION !!!
        header("Location: /404");
        exit;
    }
}
