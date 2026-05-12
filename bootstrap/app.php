<?php

declare(strict_types=1);

use App\Routes\api;
use Framework\Router;
use Framework\ServiceContainer;

$basePath = dirname(__DIR__);

if (!defined('BASE_PATH')) {
    define('BASE_PATH', $basePath);
}

$dotenv = Dotenv\Dotenv::createImmutable($basePath);
$dotenv->load();

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

require_once BASE_PATH.'/Framework/ServiceContainer.php';

require_once BASE_PATH.'/app/utils/ConfigLoader.php';

require_once BASE_PATH.'/app/Providers/ConfigServiceProvider.php';

if (!isset($container) || !$container instanceof ServiceContainer) {
    $container = new ServiceContainer();
}

(new ConfigServiceProvider())->register($container);

/*
|--------------------------------------------------------------------------
| BINDINGS
|--------------------------------------------------------------------------
*/

// $container->bind();

/*
|--------------------------------------------------------------------------
| SINGLETONS
|--------------------------------------------------------------------------
*/

// bind the container itself so Kernel receives the same instance
$container->singleton(ServiceContainer::class, fn () => $container);

// register a Router singleton and default API routes
$container->singleton(Router::class, fn () => new Router());

$router = $container->make(Router::class);

// load and register routes from routes/api.php using its prefix helper
require_once BASE_PATH.'/routes/api.php';
api::register($router);
