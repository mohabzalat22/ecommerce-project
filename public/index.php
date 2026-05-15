<?php

// Autoload → Container → Bootstrap services → Kernel → Request → Router → Controller → Response
declare(strict_types=1);

// Set JSON header before any output
header('Content-Type: application/json; charset=utf-8');

// Enable CORS for React frontend
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Handle preflight requests
if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
    http_response_code(200);

    exit;
}

require __DIR__.'/../vendor/autoload.php';

use Framework\Kernel;
use Framework\Request;
use Framework\ServiceContainer;

$container = new ServiceContainer();

require __DIR__.'/../bootstrap/app.php';

$kernel = $container->make(Kernel::class);

$request = new Request();

$response = $kernel->handle($request);

echo $response;
