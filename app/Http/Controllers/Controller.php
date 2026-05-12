<?php

declare(strict_types=1);

namespace App\Controllers;

use Framework\Response;

class Controller
{
    protected Response $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    /**
     * Send success response.
     *
     * @param null|mixed $data
     */
    protected function success($data = null, string $message = 'Success', int $statusCode = 200): string
    {
        return $this->response->success($data, $message, $statusCode);
    }

    /**
     * Send error response.
     *
     * @param null|mixed $errors
     */
    protected function error(string $message, $errors = null, int $statusCode = 400): string
    {
        return $this->response->error($message, $errors, $statusCode);
    }

    /**
     * Send paginated response.
     *
     * @param mixed $data
     */
    protected function paginated($data, int $total, int $page, int $perPage, string $message = 'Success'): string
    {
        return $this->response->paginated($data, $total, $page, $perPage, $message);
    }
}
