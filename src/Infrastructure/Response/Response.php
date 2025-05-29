<?php

namespace DemoShop\Infrastructure\Response;

/**
 * Abstract base class for all HTTP responses.
 */
abstract class Response
{
    protected int $statusCode;
    protected array $headers;

    /**
     * Response constructor.
     *
     * @param int $statusCode HTTP status code (e.g. 200, 404)
     * @param array $headers Associative array of headers
     */
    public function __construct(int $statusCode, array $headers)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Sends the response code and headers.
     *
     * @return void
     */
    public function send(): void
    {
        $this->setCode();
        $this->setHeaders();
    }

    /**
     * Sends the HTTP status code.
     *
     * @return void
     */
    protected function setCode(): void
    {
        http_response_code($this->statusCode);
    }

    /**
     * Sends the HTTP headers.
     *
     * @return void
     */
    protected function setHeaders(): void
    {
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
    }

}