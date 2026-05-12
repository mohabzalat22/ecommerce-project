<?php

declare(strict_types=1);

namespace App\Routes;

use App\Controllers\CatalogFilterController;
use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use App\Controllers\UserController;
use Framework\Router;

class Api
{
    public static function register(Router $router): void
    {
        // Home endpoints
        $router->get('/api/v1.0.0/home', [HomeController::class, 'index']);
        $router->post('/api/v1.0.0/home', [HomeController::class, 'store']);
        $router->delete('/api/v1.0.0/home', [HomeController::class, 'destroy']);

        // API v1.0.0 group
        $router->group('/api/v1.0.0', function (Router $router): void {
            $router->get('/storefront/home', [HomeController::class, 'storefront']);

            $router->group('/filters', function (Router $router): void {
                $router->get('/categories', [CatalogFilterController::class, 'categories']);
                $router->get('/colors', [CatalogFilterController::class, 'colors']);
                $router->get('/sizes', [CatalogFilterController::class, 'sizes']);
                $router->get('/attributes', [CatalogFilterController::class, 'filterableAttributes']);
            });

            // Category endpoints
            $router->group('/categories', function (Router $router): void {
                $router->get('', [CategoryController::class, 'index']);
                $router->get('/{id}', [CategoryController::class, 'show']);
                $router->post('', [CategoryController::class, 'store']);
                $router->put('/{id}', [CategoryController::class, 'update']);
                $router->delete('/{id}', [CategoryController::class, 'destroy']);
            });

            // Product endpoints
            $router->group('/products', function (Router $router): void {
                $router->get('', [ProductController::class, 'index']);
                $router->get('/{id}/related', [ProductController::class, 'related']);
                $router->get('/{id}', [ProductController::class, 'show']);
                $router->post('', [ProductController::class, 'store']);
                $router->put('/{id}', [ProductController::class, 'update']);
                $router->delete('/{id}', [ProductController::class, 'destroy']);
            });

            // User endpoints
            $router->group('/users', function (Router $router): void {
                $router->get('', [UserController::class, 'index']);
                $router->get('/{id}', [UserController::class, 'show']);
                $router->post('', [UserController::class, 'store']);
                $router->put('/{id}', [UserController::class, 'update']);
                $router->delete('/{id}', [UserController::class, 'destroy']);
            });
        });
    }
}
