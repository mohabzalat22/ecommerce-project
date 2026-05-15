<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use Framework\Request;

class UserController extends Controller
{
    /**
     * List all users.
     */
    public function index(Request $request)
    {
        try {
            $users = User::all();

            return $this->success([
                'items' => $users,
                'count' => count($users),
            ], 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve users', ['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get a single user by ID.
     */
    public function show(Request $request)
    {
        try {
            $userId = $request->param('id');

            if (!$userId) {
                return $this->error('User ID is required', null, 400);
            }

            $user = User::find($userId);

            if (!$user) {
                return $this->error('User not found', null, 404);
            }

            return $this->success($user, 'User retrieved successfully');
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create a new user (admin / API; persists to users table).
     */
    public function store(Request $request)
    {
        try {
            $data = $request->json();
            $name = trim((string) ($data['name'] ?? ''));
            $email = trim((string) ($data['email'] ?? ''));
            $password = (string) ($data['password'] ?? '');

            if ('' === $name || '' === $email) {
                return $this->error('Name and email are required', null, 422);
            }

            if ('' === $password) {
                return $this->error('Password is required', null, 422);
            }

            if (User::where('email', $email)->exists()) {
                return $this->error('Email already exists', null, 409);
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'email_verified_at' => $data['email_verified_at'] ?? null,
                'remember_token' => null,
            ]);

            return $this->success(
                [
                    'id' => (int) $user->id,
                    'name' => (string) $user->name,
                    'email' => (string) $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ],
                'User created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error('Failed to create user', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a user.
     */
    public function update(Request $request)
    {
        try {
            $userId = $request->param('id');
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$userId) {
                return json_encode([
                    'success' => false,
                    'message' => 'User ID is required',
                ]);
            }

            $user = User::find($userId);

            if (!$user) {
                return json_encode([
                    'success' => false,
                    'message' => 'User not found',
                ]);
            }

            // Check if new email is already taken by another user
            if (isset($data['email']) && $data['email'] !== $user->email) {
                if (User::where('email', $data['email'])->exists()) {
                    return json_encode([
                        'success' => false,
                        'message' => 'Email already exists',
                    ]);
                }
            }

            $updateData = [
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
                'email_verified_at' => $data['email_verified_at'] ?? $user->email_verified_at,
            ];

            // Only update password if provided
            if (isset($data['password'])) {
                $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            }

            $user->update($updateData);

            return json_encode([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete a user.
     */
    public function destroy(Request $request)
    {
        try {
            $userId = $request->param('id');

            if (!$userId) {
                return json_encode([
                    'success' => false,
                    'message' => 'User ID is required',
                ]);
            }

            $user = User::find($userId);

            if (!$user) {
                return json_encode([
                    'success' => false,
                    'message' => 'User not found',
                ]);
            }

            $user->delete();

            return json_encode([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
