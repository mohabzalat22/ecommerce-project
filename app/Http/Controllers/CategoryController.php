<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use Framework\Request;

class CategoryController extends Controller
{
    /**
     * List all categories.
     */
    public function index(Request $request)
    {
        try {
            $categories = Category::with('children', 'products')->get();

            return json_encode([
                'success' => true,
                'data' => $categories,
                'count' => count($categories),
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get a single category by ID.
     */
    public function show(Request $request)
    {
        try {
            $categoryId = $request->param('id');

            if (!$categoryId) {
                return json_encode([
                    'success' => false,
                    'message' => 'Category ID is required',
                ]);
            }

            $category = Category::with('parent', 'children', 'products', 'attributes')
                ->find($categoryId)
            ;

            if (!$category) {
                return json_encode([
                    'success' => false,
                    'message' => 'Category not found',
                ]);
            }

            return json_encode([
                'success' => true,
                'data' => $category,
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create a new category.
     */
    public function store(Request $request)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || !isset($data['name'])) {
                return json_encode([
                    'success' => false,
                    'message' => 'Name is required',
                ]);
            }

            $category = Category::create([
                'parent_id' => $data['parent_id'] ?? null,
                'name' => $data['name'],
                'slug' => $data['slug'] ?? $this->generateSlug($data['name']),
                'image_url' => $data['image_url'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'sort_order' => $data['sort_order'] ?? 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return json_encode([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category,
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update a category.
     */
    public function update(Request $request)
    {
        try {
            $categoryId = $request->param('id');
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$categoryId) {
                return json_encode([
                    'success' => false,
                    'message' => 'Category ID is required',
                ]);
            }

            $category = Category::find($categoryId);

            if (!$category) {
                return json_encode([
                    'success' => false,
                    'message' => 'Category not found',
                ]);
            }

            $category->update([
                'parent_id' => $data['parent_id'] ?? $category->parent_id,
                'name' => $data['name'] ?? $category->name,
                'slug' => $data['slug'] ?? $category->slug,
                'image_url' => $data['image_url'] ?? $category->image_url,
                'description' => $data['description'] ?? $category->description,
                'is_active' => $data['is_active'] ?? $category->is_active,
                'sort_order' => $data['sort_order'] ?? $category->sort_order,
            ]);

            return json_encode([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category,
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete a category.
     */
    public function destroy(Request $request)
    {
        try {
            $categoryId = $request->param('id');

            if (!$categoryId) {
                return json_encode([
                    'success' => false,
                    'message' => 'Category ID is required',
                ]);
            }

            $category = Category::find($categoryId);

            if (!$category) {
                return json_encode([
                    'success' => false,
                    'message' => 'Category not found',
                ]);
            }

            $category->delete();

            return json_encode([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate slug from name.
     */
    protected function generateSlug(string $name): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }
}
