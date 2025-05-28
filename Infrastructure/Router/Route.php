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
    private $callable;
    private array $middlewares;
    private string $regex;

    /**
     * Constructs a new Route instance.
     *
     * @param string $pattern The URL pattern (may contain parameters like /{id}).
     * @param callable $callable The target controller method to execute.
     * @param array $middlewares List of middleware class names to run before the callable.
     */
    public function __construct(
        string $pattern,
        callable $callable,
        array $middlewares = []
    ) {
        $this->pattern = $pattern;
        $this->callable = $callable;
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
     * Extracts named parameters from the URL based on the route pattern.
     *
     * @param string $url The URL to extract parameters from.
     *
     * @return array An associative array of matched parameters.
     */
    public function extractParams(string $url): array
    {
        preg_match($this->regex, $url, $matches);
        return array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
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
    public function getPattern(): string { return $this->pattern; }

    /**
     * Gets the callable (controller method) associated with this route.
     *
     * @return callable
     */
    public function getCallable(): callable { return $this->callable; }

    /**
     * Gets the list of middleware associated with this route.
     *
     * @return array List of class names or middleware instances.
     */
    public function getMiddlewares(): array { return $this->middlewares; }
}