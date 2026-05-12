<?php

declare(strict_types=1);

namespace App\Routes;

use App\Controllers\HomeController;
use Framework\Router;

class Api
{
    public static function register(Router $router): void
    {
        $self = new self();

        $router->get($self->prefix('/home'), [HomeController::class, 'index']);
        $router->post($self->prefix('/home'), [HomeController::class, 'store']);
        $router->delete($self->prefix('/home'), [HomeController::class, 'destroy']);
    }

    protected function prefix(string $url)
    {
        return '/api/v1.0.0'.$url;
    }
}
