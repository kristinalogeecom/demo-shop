<?php

namespace DemoShop\Infrastructure\Router;

/**
 * Represents a single route definition within the application.
 *
 * A route contains:
 * - A URL pattern (e.g. /admin/categories/{id})
 * - A callable (controller method)
 * - Optional middleware array
 * - A compiled regex pattern for matching and parameter extraction
 */
class Route
{
    private string $pattern;
    private array $controllerAction;
    private array $middlewares;
    private string $regex;

    /**
     * Constructs a new Route instance.
     *
     * @param string $pattern The URL pattern (may contain parameters like /{id}).
     * @param array $controllerAction The target controller method to execute.
     * @param array $middlewares List of middleware class names to run before the callable.
     */
    public function __construct(
        string $pattern,
        array  $controllerAction,
        array  $middlewares = []
    ) {
        $this->pattern = $pattern;
        $this->controllerAction = $controllerAction;
        $this->middlewares = $middlewares;
        $this->regex = $this->compilePattern($pattern);
    }

    /**
     * Checks if the provided URL matches this route's pattern.
     *
     * @param string $url The requested URL.
     *
     * @return bool True if the URL matches, false otherwise.
     */
    public function matches(string $url): bool
    {
        return (bool) preg_match($this->regex, $url);
    }

    /**
     * Extracts named route parameters from the URL based on the route's regex pattern.
     *
     *  For example, if the pattern is "/category/{id}" and the URL is "/category/42",
     *  this method will return ['id' => '42'].
     *
     * @param string $url The URL to extract parameters from.
     *
     * @return array<string, string>  An associative array of matched route parameters.
     */
    public function extractParams(string $url): array
    {
        preg_match($this->regex, $url, $matchesWithKeys);

        return array_filter(
            $matchesWithKeys,
            fn(string|int $key): bool => !is_int($key),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Compiles a route pattern into a regular expression.
     *
     * Supports patterns like /admin/{id} or /product/{slug:[a-z0-9\-]+}
     *
     * @param string $pattern The route pattern.
     *
     * @return string The compiled regex.
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

    /**
     * Gets the original pattern string (e.g. "/admin/categories/{id}").
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Gets the controller and method associated with this route.
     *
     * @return array
     */
    public function getControllerAction(): array
    {
        return $this->controllerAction;
    }

    /**
     * Gets the list of middleware associated with this route.
     *
     * @return array List of class names or middleware instances.
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}