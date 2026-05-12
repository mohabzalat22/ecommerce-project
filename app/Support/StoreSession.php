<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Shared PHP session bootstrap for auth routes and HTTP middleware.
 */
final class StoreSession
{
    public static function ensure(bool $persistent = false): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name('ecom_store');
        session_set_cookie_params([
            'lifetime' => $persistent ? (60 * 60 * 24 * 14) : 0,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}
