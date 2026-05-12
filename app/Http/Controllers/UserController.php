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
     * Create a new user.
     */
    public function store(Request $request)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            if (!$data || !isset($data['name']) || !isset($data['email'])) {
                return json_encode([
                    'success' => false,
                    'message' => 'Name and email are required',
                ]);
            }

            // Check if email already exists
            if (User::where('email', $data['email'])->exists()) {
                return json_encode([
                    'success' => false,
                    'message' => 'Email already exists',
                ]);
            }

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => isset($data['password']) ? password_hash($data['password'], PASSWORD_BCRYPT) : null,
                'email_verified_at' => $data['email_verified_at'] ?? null,
                'remember_token' => null,
            ]);

            return json_encode([
                'success' => true,
                'message' => 'User created successfully',
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
