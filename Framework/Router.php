<?php

declare(strict_types=1);

namespace Framework;

class Router
{
    protected array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    /**
     * @var array<string, array<string, list<class-string<MiddlewareInterface>>>>
     */
    protected array $routeMiddleware = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    protected string $currentPrefix = '';

    /** @var list<class-string<MiddlewareInterface>> */
    protected array $groupMiddlewareStack = [];

    public function group(string $prefix, \Closure $callback, array $middleware = []): void
    {
        $previousPrefix = $this->currentPrefix;
        $previousStack = $this->groupMiddlewareStack;
        $this->currentPrefix = $previousPrefix.$prefix;
        $this->groupMiddlewareStack = array_merge($previousStack, $middleware);

        $callback($this);

        $this->currentPrefix = $previousPrefix;
        $this->groupMiddlewareStack = $previousStack;
    }

    /**
     * @param array<class-string<MiddlewareInterface>> $middleware
     */
    public function get(string $uri, array|string $action, array $middleware = []): void
    {
        $this->registerRoute('GET', $uri, $action, $middleware);
    }

    /**
     * @param array<class-string<MiddlewareInterface>> $middleware
     */
    public function post(string $uri, array|string $action, array $middleware = []): void
    {
        $this->registerRoute('POST', $uri, $action, $middleware);
    }

    /**
     * @param array<class-string<MiddlewareInterface>> $middleware
     */
    public function put(string $uri, array|string $action, array $middleware = []): void
    {
        $this->registerRoute('PUT', $uri, $action, $middleware);
    }

    /**
     * @param array<class-string<MiddlewareInterface>> $middleware
     */
    public function delete(string $uri, array|string $action, array $middleware = []): void
    {
        $this->registerRoute('DELETE', $uri, $action, $middleware);
    }

    public function dispatch(Request $request, ServiceContainer $container)
    {
        $method = $request->method();
        $uri = $request->uri();

        $routeKey = $uri;
        $action = $this->routes[$method][$uri] ?? null;

        if (!$action) {
            $matched = $this->matchPattern($method, $uri, $request);
            if ($matched) {
                [$action, $routeKey] = $matched;
            }
        }

        if (!$action) {
            $response = new Response();

            return $response->error("Route not found: {$uri}", null, 404);
        }

        foreach ($this->routeMiddleware[$method][$routeKey] ?? [] as $middlewareClass) {
            /** @var MiddlewareInterface $pipe */
            $pipe = $container->make($middlewareClass);
            $halt = $pipe->handle($request, $container);
            if (null !== $halt) {
                return $halt;
            }
        }

        if ($action instanceof \Closure) {
            return $action($container, $request);
        }

        if (is_array($action)) {
            [$controllerClass, $methodName] = $action;
            $controller = $container->make($controllerClass);

            return $this->callAction(
                $controller,
                $methodName,
                $container,
                $request
            );
        }

        $response = new Response();

        return $response->error('Invalid route action', null, 500);
    }

    /**
     * @param array<class-string<MiddlewareInterface>> $middleware
     */
    protected function registerRoute(string $method, string $uri, array|string $action, array $middleware): void
    {
        $key = $this->currentPrefix.$uri;
        $this->routes[$method][$key] = $action;
        $merged = array_merge($this->groupMiddlewareStack, $middleware);
        if ([] !== $merged) {
            $this->routeMiddleware[$method][$key] = $merged;
        }
    }

    /**
     * Match URI against route patterns and extract parameters.
     *
     * @return null|array{0: array|string, 1: string} [action, pattern]
     */
    protected function matchPattern(string $method, string $uri, Request $request): ?array
    {
        foreach ($this->routes[$method] as $pattern => $action) {
            // Convert route pattern to regex
            // e.g., /api/v1.0.0/categories/{id} becomes regex pattern
            // Allow non-numeric IDs (e.g. order UUIDs) while still matching integer resource routes.
            $regexPattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
            $regexPattern = '@^'.$regexPattern.'$@';

            if (preg_match($regexPattern, $uri, $matches)) {
                // Extract parameters and store in request
                foreach ($matches as $key => $value) {
                    if (!is_numeric($key)) {
                        $request->setParam($key, $value);
                    }
                }

                return [$action, $pattern];
            }
        }

        return null;
    }

    protected function callAction(
        object $controller,
        string $method,
        ServiceContainer $container,
        Request $request
    ) {
        $reflection = new \ReflectionMethod($controller, $method);
        $parameters = [];

        foreach ($reflection->getParameters() as $param) {
            $type = $param->getType();

            if (!$type) {
                throw new \Exception("cannot resolve parameter {$param->getName()}");
            }

            $className = $type->getName();

            if (Request::class == $className) {
                $parameters[] = $request;

                continue;
            }

            $parameters[] = $container->make($className);
        }

        return $reflection->invokeArgs($controller, $parameters);
    }
}
