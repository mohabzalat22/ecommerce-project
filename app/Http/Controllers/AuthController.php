<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Support\StoreSession;
use Framework\Request;

class AuthController extends Controller
{
    private const MIN_PASSWORD = 8;

    /** @return array{id: int, name: string, email: string, role: string} */
    private function userPayload(User $user): array
    {
        return [
            'id' => (int) $user->id,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'role' => (string) ($user->role ?? 'customer'),
        ];
    }

    public function register(Request $request)
    {
        $data = $request->json();
        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $remember = !empty($data['remember']);

        if ($name === '' || $email === '' || $password === '') {
            return $this->error('Name, email, and password are required', null, 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('Invalid email address', null, 422);
        }

        if (strlen($password) < self::MIN_PASSWORD) {
            return $this->error('Password must be at least '.self::MIN_PASSWORD.' characters', null, 422);
        }

        if (User::where('email', $email)->exists()) {
            return $this->error('An account with this email already exists', null, 409);
        }

        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'email_verified_at' => null,
                'remember_token' => null,
                'role' => 'customer',
            ]);
        } catch (\Throwable $e) {
            return $this->error('Could not create account', ['error' => $e->getMessage()], 500);
        }

        StoreSession::ensure($remember);
        $_SESSION['user_id'] = (int) $user->id;

        return $this->success(['user' => $this->userPayload($user)], 'Account created', 201);
    }

    public function login(Request $request)
    {
        $data = $request->json();
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');
        $remember = !empty($data['remember']);

        if ($email === '' || $password === '') {
            return $this->error('Email and password are required', null, 422);
        }

        $user = User::where('email', $email)->first();

        if (!$user || !password_verify($password, (string) $user->getAttribute('password'))) {
            return $this->error('Invalid email or password', null, 401);
        }

        StoreSession::ensure($remember);
        $_SESSION['user_id'] = (int) $user->id;

        return $this->success(['user' => $this->userPayload($user)], 'Signed in');
    }

    public function session(Request $request)
    {
        StoreSession::ensure(false);

        if (empty($_SESSION['user_id'])) {
            return $this->success(['authenticated' => false], 'OK');
        }

        $user = User::find($_SESSION['user_id']);

        if (!$user) {
            unset($_SESSION['user_id']);

            return $this->success(['authenticated' => false], 'OK');
        }

        return $this->success([
            'authenticated' => true,
            'user' => $this->userPayload($user),
        ], 'OK');
    }

    public function logout(Request $request)
    {
        StoreSession::ensure(false);

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool) $p['secure'], (bool) $p['httponly']);
        }

        session_destroy();

        return $this->success(null, 'Signed out');
    }
}
