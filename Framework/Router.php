<?php

declare(strict_types=1);

namespace Framework;

class Router
{
    protected array $routes = [
        'GET' => [],
        'POST' => [],
        'DELETE' => [],
    ];

    public function get(string $uri, array|string $action): void
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function post(string $uri, array|string $action): void
    {
        $this->routes['POST'][$uri] = $action;
    }

    public function delete(string $uri, array|string $action): void
    {
        $this->routes['DELETE'][$uri] = $action;
    }

    public function dispatch(Request $request, ServiceContainer $container)
    {
        $method = $request->method();
        $uri = $request->uri();
        $action = $this->routes[$method][$uri] ?? null;

        if (!$action) {
            throw new \Exception("404 NOT FOUND: {$uri}");
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

        throw new \Exception('Invaliud Route action');
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
