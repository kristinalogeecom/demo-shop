<?php

namespace DemoShop\Infrastructure\Router;

use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use DemoShop\Infrastructure\Response\Response;
use Exception;

class RouteDispatcher
{
    private Request $request;
    private array $routes = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function register(string $method, Route $route): void
    {
        $this->routes[$method][] = $route;
    }

    /**
     * @throws Exception
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
