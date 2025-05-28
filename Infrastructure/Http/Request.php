<?php

namespace DemoShop\Infrastructure\Http;

/**
 * Request class encapsulates HTTP request data,
 * such as method, URL, query params, and body.
 */
class Request
{
    protected string $method;
    protected string $url;
    protected array $body;
    protected array $queryParams;
    protected array $routeParams = [];

    /**
     * Parses the global $_SERVER, $_POST, and $_GET
     * variables to initialize the request object.
     */
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->queryParams = $_GET;

        if ($this->isJson()) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            $this->body = is_array($decoded) ? $decoded : [];
        } else {
            $this->body = $_POST;
        }
    }

    /**
     * Returns the HTTP method.
     *
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Returns the request URL path.
     *
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * Returns a value from the POST body.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    /**
     * Returns a value from the GET query string.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }

    /**
     * Sets parameters extracted from route placeholders.
     *
     * @param array $params
     *
     * @return void
     */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    /**
     * Returns a route parameter value.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function param(string $key, mixed$default = null): mixed
    {
        return $this->routeParams[$key] ?? $default;
    }

    /**
     * Checks if the request is JSON
     *
     * @return bool
     */
    public function isJson(): bool
    {
        return isset($_SERVER['CONTENT_TYPE']) &&
            str_starts_with($_SERVER['CONTENT_TYPE'], 'application/json');
    }

    /**
     * Returns all data from the body
     *
     * @return array
     */
    public function all(): array
    {
        return $this->body;
    }

    /**
     * Returns only specific fields
     *
     * @param array $keys
     * @return array
     */
    public function only(array $keys): array
    {
        return array_filter(
            $this->body,
            fn($key) => in_array($key, $keys),
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Checks if a field is empty and exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->body[$key]) && trim($this->body[$key]) !== '';
    }

    /**
     * Returns all route parameters as array.
     *
     * @return array
     */
    public function routeParams(): array
    {
        return $this->routeParams;
    }


}
