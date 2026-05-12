<?php

declare(strict_types=1);

use Framework\ServiceContainer;

class ConfigServiceProvider
{
    public function register(ServiceContainer $container): void
    {
        $container->singleton('config', function () {
            $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);

            if (!function_exists('env')) {
                function env(string $key, mixed $default = null): mixed
                {
                    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
                }
            }

            $loader = new ConfigLoader();
            $loader->load($basePath.'/config');

            return $loader;
        });
    }
}
