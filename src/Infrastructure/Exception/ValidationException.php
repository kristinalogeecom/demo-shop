<?php

namespace DemoShop\Infrastructure\Exception;

class ValidationException extends Exception
{
    private array $errors;

    public function __construct(string|array $errors)
    {
        $this->errors = is_array($errors) ? $errors : [$errors];
        parent::__construct("Validation failed");
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}