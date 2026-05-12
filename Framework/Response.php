<?php

declare(strict_types=1);

namespace Framework;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];

    public function __construct()
    {
        $this->setHeader('Content-Type', 'application/json');
    }

    /**
     * Set HTTP status code.
     */
    public function setStatus(int $code): self
    {
        $this->statusCode = $code;

        return $this;
    }

    /**
     * Set response header.
     */
    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Success response.
     *
     * @param null|mixed $data
     */
    public function success($data = null, string $message = 'Success', int $statusCode = 200): string
    {
        $this->statusCode = $statusCode;
        $response = [
            'success' => true,
            'status_code' => $statusCode,
            'message' => $message,
        ];

        if (null !== $data) {
            $response['data'] = $data;
        }

        return $this->send($response);
    }

    /**
     * Error response.
     *
     * @param null|mixed $errors
     */
    public function error(string $message, $errors = null, int $statusCode = 400): string
    {
        $this->statusCode = $statusCode;
        $response = [
            'success' => false,
            'status_code' => $statusCode,
            'message' => $message,
        ];

        if (null !== $errors) {
            $response['errors'] = $errors;
        }

        return $this->send($response);
    }

    /**
     * Paginated response.
     *
     * @param mixed $data
     */
    public function paginated($data, int $total, int $page, int $perPage, string $message = 'Success'): string
    {
        $totalPages = ceil($total / $perPage);
        $response = [
            'success' => true,
            'status_code' => 200,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => $totalPages,
            ],
        ];

        return $this->send($response);
    }

    /**
     * Send response with headers and status code.
     */
    private function send(array $response): string
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        return json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
