<?php

namespace IdapGroup\HelixApiBundle\Bundle;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    /**
     * @param $data
     * @param bool $status
     * @param string $message
     * @param int $statusCode
     * @param array $headers
     * @param bool $json
     */
    public function __construct($data = [], bool $status = true, string $message = '',  int $statusCode = 200, array $headers = [], bool $json = false)
    {
        $responseData = ['status' => $status, 'message' => $message, 'data' => $data, 'code' => $statusCode];

        parent::__construct($responseData, $statusCode, $headers, $json);
    }

    /**
     * @param string $message
     * @param array $data
     * @return self
     */
    public static function successful(string $message = 'successful', array $data = []): self
    {
        return new self($data, true, $message);
    }
}