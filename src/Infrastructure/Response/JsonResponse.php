<?php

namespace DemoShop\Infrastructure\Response;

use DemoShop\Infrastructure\Exception\ResponseException;

/**
 * Represents a JSON API response.
 */
class JsonResponse extends Response
{
    protected mixed $data;

    /**
     * JsonResponse constructor.
     *
     * @param mixed $data
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(mixed $data = [], int $statusCode = 200, array $headers = [])
    {
        $headers['Content-Type'] = 'application/json';
        $this->data = $data;
        parent::__construct($statusCode, $headers);
    }

    /**
     * Sends the JSON response.
     *
     * @return void
     *
     * @throws ResponseException
     */
    public function send(): void
    {
        parent::send();
        $json = json_encode($this->data, JSON_UNESCAPED_UNICODE);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ResponseException(
                new self([
                    'error' => 'Failed to encode response as JSON',
                    'details' => json_last_error_msg()
                ], 500)
            );
        }

        echo $json;
    }

}