<?php

declare(strict_types=1);

class ConfigLoader
{
    protected array $items = [];

    public function load(string $configPath): void
    {
        $files = glob(rtrim($configPath, '/').'/*.php') ?: [];
        sort($files);

        foreach ($files as $file) {
            $key = basename($file, '.php');
            $config = require $file;

            if (!is_array($config)) {
                throw new RuntimeException("Config file {$file} must return an array.");
            }

            $this->items[$key] = $config;
        }
    }

    public function all(): array
    {
        return $this->items;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        [$found, $value] = $this->resolve($key);

        return $found ? $value : $default;
    }

    public function has(string $key): bool
    {
        [$found] = $this->resolve($key);

        return $found;
    }

    public function set(string $key, mixed $value): void
    {
        $parts = explode('.', $key);
        $lastIndex = count($parts) - 1;
        $target = &$this->items;

        foreach ($parts as $index => $part) {
            if ($index === $lastIndex) {
                $target[$part] = $value;

                return;
            }

            if (!isset($target[$part]) || !is_array($target[$part])) {
                $target[$part] = [];
            }

            $target = &$target[$part];
        }
    }

    protected function resolve(string $key): array
    {
        if ('' == $key) {
            return [true, $this->items];
        }

        $parts = explode('.', $key);
        $value = $this->items;

        foreach ($parts as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return [false, null];
            }

            $value = $value[$part];
        }

        return [true, $value];
    }
}
