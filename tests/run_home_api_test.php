<?php

declare(strict_types=1);

// Simple runnable test script that boots the app and simulates requests
require __DIR__.'/../vendor/autoload.php';

use Framework\Kernel;
use Framework\Request;
use Framework\ServiceContainer;

// some environments may not load the app/Framework classes via Composer autoload,
// so require them directly for this standalone test runner.
require_once __DIR__.'/../Framework/ServiceContainer.php';

require_once __DIR__.'/../Framework/Request.php';

require_once __DIR__.'/../Framework/Router.php';

require_once __DIR__.'/../Framework/Kernel.php';

$container = new ServiceContainer();

require __DIR__.'/../bootstrap/app.php';

$kernel = $container->make(Kernel::class);

function runRequest($method, $uri)
{
    $_SERVER['REQUEST_METHOD'] = $method;
    $_SERVER['REQUEST_URI'] = $uri;
    // clear input and request arrays
    $_REQUEST = [];

    $request = new Request();
    global $kernel;
    $response = $kernel->handle($request);

    echo "{$method} {$uri} -> {$response}".PHP_EOL;

    $decoded = json_decode($response, true);

    return $decoded;
}

// GET (use prefixed API path)
$get = runRequest('GET', '/api/v1.0.0/home');
if (!isset($get['data']['message']) || 'hello' !== $get['data']['message']) {
    echo "GET /api/home test failed\n";

    exit(1);
}

// POST
$post = runRequest('POST', '/api/v1.0.0/home');
if (!isset($post['data']['message']) || 'post received' !== $post['data']['message']) {
    echo "POST /api/home test failed\n";

    exit(1);
}

// DELETE
$del = runRequest('DELETE', '/api/v1.0.0/home');
if (!isset($del['data']['message']) || 'resource deleted' !== $del['data']['message']) {
    echo "DELETE /api/home test failed\n";

    exit(1);
}

echo "All home API tests passed\n";

exit(0);
