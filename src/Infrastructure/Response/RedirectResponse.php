<?php

namespace DemoShop\Infrastructure\Response;

/**
 * Represents an HTTP redirect response.
 */
class RedirectResponse extends Response
{

    /**
     * RedirectResponse constructor.
     *
     * @param string $location URL to redirect to
     * @param int $status HTTP status code (default 302 - Found)
     */
    public function __construct(string $location, int $status = 302)
    {
        parent::__construct($status, ['Location' => $location]);
    }
}