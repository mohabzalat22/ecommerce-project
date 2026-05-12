<?php

declare(strict_types=1);

/**
 * Router for PHP Built-in Server with -t public
 * Routes all requests through index.php unless the file/directory exists.
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

$publicPath = __DIR__.'/public'.$uri;

// If the requested file exists under public/, let the built-in server serve it
if ('/' !== $uri && is_file($publicPath)) {
    return false;
}

// Otherwise, route through index.php
require __DIR__.'/public/index.php';
