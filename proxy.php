<?php

declare(strict_types=1);

/**
 * Router for PHP Built-in Server with -t public
 * Routes all requests through index.php unless the file/directory exists.
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If the requested file/directory exists, serve it
if ('/' !== $uri && file_exists(__DIR__.$uri)) {
    return false;
}

// Otherwise, route through index.php
require __DIR__.'/public/index.php';
