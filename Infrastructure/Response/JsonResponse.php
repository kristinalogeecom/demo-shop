<?php

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
     */
    public function send(): void
    {
        parent::send();
        echo json_encode($this->data);
    }

}