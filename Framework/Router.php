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

    protected string $currentPrefix = '';

    public function group(string $prefix, \Closure $callback): void
    {
        $previousPrefix = $this->currentPrefix;
        $this->currentPrefix = $previousPrefix.$prefix;

        $callback($this);

        $this->currentPrefix = $previousPrefix;
    }

    public function get(string $uri, array|string $action): void
    {
        $this->routes['GET'][$this->currentPrefix.$uri] = $action;
    }

    public function post(string $uri, array|string $action): void
    {
        $this->routes['POST'][$this->currentPrefix.$uri] = $action;
    }

    public function put(string $uri, array|string $action): void
    {
        $this->routes['PUT'][$this->currentPrefix.$uri] = $action;
    }

    public function delete(string $uri, array|string $action): void
    {
        $this->routes['DELETE'][$this->currentPrefix.$uri] = $action;
    }

    public function dispatch(Request $request, ServiceContainer $container)
    {
        $method = $request->method();
        $uri = $request->uri();

        // First try exact match
        $action = $this->routes[$method][$uri] ?? null;

        // If no exact match, try pattern matching
        if (!$action) {
            $action = $this->matchPattern($method, $uri, $request);
        }

        if (!$action) {
            $response = new Response();

            return $response->error("Route not found: {$uri}", null, 404);
        }

        if ($action instanceof \Closure) {
            return $action($container, $request);
        }

        if (is_array($action)) {
            [$controllerClass, $method] = $action;
            $controller = $container->make($controllerClass);

            return $this->callAction(
                $controller,
                $method,
                $container,
                $request
            );
        }

        $response = new Response();

        return $response->error('Invalid route action', null, 500);
    }

    /**
     * Match URI against route patterns and extract parameters.
     */
    protected function matchPattern(string $method, string $uri, Request $request)
    {
        foreach ($this->routes[$method] as $pattern => $action) {
            // Convert route pattern to regex
            // e.g., /api/v1.0.0/categories/{id} becomes regex pattern
            $regexPattern = preg_replace('/\{(\w+)\}/', '(?P<$1>\d+)', $pattern);
            $regexPattern = '@^'.$regexPattern.'$@';

            if (preg_match($regexPattern, $uri, $matches)) {
                // Extract parameters and store in request
                foreach ($matches as $key => $value) {
                    if (!is_numeric($key)) {
                        $request->setParam($key, $value);
                    }
                }

                return $action;
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
