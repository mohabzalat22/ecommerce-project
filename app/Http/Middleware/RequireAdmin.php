<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\StoreSession;
use Framework\MiddlewareInterface;
use Framework\Request;
use Framework\Response;
use Framework\ServiceContainer;

/**
 * Ensures the session user exists and has the admin role.
 */
final class RequireAdmin implements MiddlewareInterface
{
    public function handle(Request $request, ServiceContainer $container): ?string
    {
        StoreSession::ensure(false);

        if (empty($_SESSION['user_id'])) {
            return (new Response())->error('Authentication required', null, 401);
        }

        $user = User::find($_SESSION['user_id']);

        if (!$user || !$user->isAdmin()) {
            return (new Response())->error('Administrator access required', null, 403);
        }

        return null;
    }
}
