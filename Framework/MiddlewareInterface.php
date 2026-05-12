<?php

declare(strict_types=1);

namespace Framework;

interface MiddlewareInterface
{
    /**
     * @return string|null JSON response body, or null to continue to the controller
     */
    public function handle(Request $request, ServiceContainer $container): ?string;
}
