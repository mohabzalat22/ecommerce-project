<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\StoreSession;
use Framework\MiddlewareInterface;
use Framework\Request;
use Framework\Response;
use Framework\ServiceContainer;

/**
 * Ensures a valid storefront session (logged-in user).
 */
final class Authenticate implements MiddlewareInterface
{
    public function handle(Request $request, ServiceContainer $container): ?string
    {
        StoreSession::ensure(false);

        if (empty($_SESSION['user_id'])) {
            return (new Response())->error('Authentication required', null, 401);
        }

        return null;
    }
}
