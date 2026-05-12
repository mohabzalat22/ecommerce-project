<?php

declare(strict_types=1);

namespace Framework;

class Kernel
{
    public function __construct(
        private ServiceContainer $container,
        private Router $router
    ) {}

    public function handle(Request $request)
    {
        return $this->router->dispatch($request, $this->container);
    }
}
