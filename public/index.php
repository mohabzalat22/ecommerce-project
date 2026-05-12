<?php

// Autoload → Container → Bootstrap services → Kernel → Request → Router → Controller → Response
declare(strict_types=1);

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
