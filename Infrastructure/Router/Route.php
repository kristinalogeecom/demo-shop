<?php

namespace DemoShop\Infrastructure\Router;

use DemoShop\Infrastructure\Http\Request;
use DemoShop\Infrastructure\Container\ServiceRegistry;
use Exception;

class Route
{
    private string $pattern;
    private $callable;
    private array $middlewares;
    private string $regex;

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

    public function matches(string $url): bool
    {
        return (bool) preg_match($this->regex, $url);
    }

    public function extractParams(string $url): array
    {
        preg_match($this->regex, $url, $matches);
        return array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);
    }

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

    public function getPattern(): string { return $this->pattern; }
    public function getCallable(): callable { return $this->callable; }
    public function getMiddlewares(): array { return $this->middlewares; }
}