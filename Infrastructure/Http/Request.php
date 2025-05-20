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
        $this->body = $_POST;
        $this->queryParams = $_GET;
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
}
