<?php

declare(strict_types=1);

namespace Framework;

class Request
{
    protected array $params = [];

    public function uri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function all(): array
    {
        return $_REQUEST;
    }

    public function input(string $key, $default = null)
    {
        return $_REQUEST[$key] ?? $default;
    }

    public function param(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function setParam(string $key, $value): void
    {
        $this->params[$key] = $value;
    }

    public function json(): array
    {
        $raw = file_get_contents('php://input');

        return json_decode($raw, true) ?? [];
    }

    public function header(string $key, $default = null)
    {
        $key = 'HTTP_'.strtoupper(str_replace('-', '_', $key));

        return $_SERVER[$key] ?? $default;
    }
}
