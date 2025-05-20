<?php


/**
 * Router class responsible for defining and matching routes
 * to request URLs and methods.
 */
class Router
{
    /**
     * Stores registered routes grouped by HTTP method.
     *
     * @var array
     */
    protected array $routes = [];

    /**
     * Adds a route definition.
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $pattern URL pattern, e.g. '/user/{id}'
     * @param callable $handler Callback or controller method to handle the request
     *
     * @return void
     */
    public function addRoute(string $method, string $pattern, callable $handler): void
    {
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
            'regex' => $this->compilePattern($pattern),
        ];
    }

    /**
     * Matches the current request against defined routes
     * and invokes the matching handler.
     *
     * @param Request $request
     *
     * @return void
     */
    public function matchRoute(Request $request) : void
    {
        $method = $request->method();   // 'GET'/'POST'
        $url = rtrim($request->url(), '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['regex'], $url, $matches)) {
                $params = array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
                $request->setRouteParams($params);
                call_user_func($route['handler'], $request);
                return;
            }
        }

        include_once __DIR__ . '/../../Application/Presentation/Page/Error404.phtml';
    }

    /**
     * Converts a route pattern with parameters to a regular expression.
     *
     * Example: '/user/{id}' => '/^\/user\/(?P<id>[^\/]+)$/'
     *
     * @param string $pattern
     *
     * @return string
     */
    private function compilePattern(string $pattern): string
    {
        if ($pattern === '/') {
            return '@^/$@D';
        }

        return '@^' . preg_replace_callback('/\{(\w+)(?::([^}]+))?}/', function ($matches) {
                $name = $matches[1];
                $constraint = $matches[2] ?? '[^/]+';
                return '(?P<' . $name . '>' . $constraint . ')';
            }, rtrim($pattern, '/')) . '$@D';
    }

}
