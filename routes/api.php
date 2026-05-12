<?php

declare(strict_types=1);

namespace App\Routes;

use App\Controllers\AuthController;
use App\Controllers\CatalogFilterController;
use App\Controllers\CategoryController;
use App\Controllers\EavAttributeController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\ProductEavValueController;
use App\Controllers\ProductImageController;
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
            $router->post('/auth/register', [AuthController::class, 'register']);
            $router->post('/auth/login', [AuthController::class, 'login']);
            $router->get('/auth/session', [AuthController::class, 'session']);
            $router->post('/auth/logout', [AuthController::class, 'logout']);

            $router->get('/storefront/home', [HomeController::class, 'storefront']);

            $router->group('/filters', function (Router $router): void {
                $router->get('/categories', [CatalogFilterController::class, 'categories']);
                $router->get('/colors', [CatalogFilterController::class, 'colors']);
                $router->get('/sizes', [CatalogFilterController::class, 'sizes']);
                $router->get('/attributes', [CatalogFilterController::class, 'filterableAttributes']);
            });

            // EAV attribute definitions (+ options) — covers eav_attributes & eav_attribute_options models
            $router->group('/eav-attributes', function (Router $router): void {
                $router->get('/{attributeId}/options', [EavAttributeController::class, 'optionsIndex']);
                $router->post('/{attributeId}/options', [EavAttributeController::class, 'optionsStore']);
                $router->put('/{attributeId}/options/{optionId}', [EavAttributeController::class, 'optionsUpdate']);
                $router->delete('/{attributeId}/options/{optionId}', [EavAttributeController::class, 'optionsDestroy']);
                $router->get('', [EavAttributeController::class, 'index']);
                $router->get('/{id}', [EavAttributeController::class, 'show']);
                $router->post('', [EavAttributeController::class, 'store']);
                $router->put('/{id}', [EavAttributeController::class, 'update']);
                $router->delete('/{id}', [EavAttributeController::class, 'destroy']);
            });

            // Category endpoints (+ category_attributes pivot)
            $router->group('/categories', function (Router $router): void {
                $router->get('', [CategoryController::class, 'index']);
                $router->get('/{id}/attributes', [CategoryController::class, 'linkedAttributes']);
                $router->put('/{id}/attributes', [CategoryController::class, 'syncAttributes']);
                $router->get('/{id}', [CategoryController::class, 'show']);
                $router->post('', [CategoryController::class, 'store']);
                $router->put('/{id}', [CategoryController::class, 'update']);
                $router->delete('/{id}', [CategoryController::class, 'destroy']);
            });

            // Product endpoints (+ product_images & eav_product_values)
            $router->group('/products', function (Router $router): void {
                $router->get('', [ProductController::class, 'index']);
                $router->get('/{id}/related', [ProductController::class, 'related']);
                $router->get('/{id}/images', [ProductImageController::class, 'index']);
                $router->post('/{id}/images', [ProductImageController::class, 'store']);
                $router->put('/{id}/images/{imageId}', [ProductImageController::class, 'update']);
                $router->delete('/{id}/images/{imageId}', [ProductImageController::class, 'destroy']);
                $router->get('/{id}/eav-values', [ProductEavValueController::class, 'index']);
                $router->put('/{id}/eav-values', [ProductEavValueController::class, 'sync']);
                $router->get('/{id}', [ProductController::class, 'show']);
                $router->post('', [ProductController::class, 'store']);
                $router->put('/{id}', [ProductController::class, 'update']);
                $router->delete('/{id}', [ProductController::class, 'destroy']);
            });

            // User endpoints (users model)
            $router->group('/users', function (Router $router): void {
                $router->get('', [UserController::class, 'index']);
                $router->get('/{id}', [UserController::class, 'show']);
                $router->post('', [UserController::class, 'store']);
                $router->put('/{id}', [UserController::class, 'update']);
                $router->delete('/{id}', [UserController::class, 'destroy']);
            });

            $router->get('/orders', [OrderController::class, 'index']);
            $router->get('/orders/{id}', [OrderController::class, 'show']);
            $router->put('/orders/{id}', [OrderController::class, 'update']);
            $router->delete('/orders/{id}', [OrderController::class, 'destroy']);
            $router->post('/orders', [OrderController::class, 'store']);
        });
    }
}
