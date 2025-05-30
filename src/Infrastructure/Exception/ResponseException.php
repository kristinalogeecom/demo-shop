<?php

namespace DemoShop\Infrastructure\Exception;

use DemoShop\Infrastructure\Response\Response;

class ResponseException extends Exception
{
    private Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
        parent::__construct("Middleware returned a response.");
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}