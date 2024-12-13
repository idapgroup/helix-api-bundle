<?php

namespace IdapGroup\HelixApiBundle\Exception;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseException extends RuntimeException
{
    public function __construct(string $message, public ?array $data = [])
    {
        parent::__construct($message,
            Response::HTTP_BAD_REQUEST);
    }
}