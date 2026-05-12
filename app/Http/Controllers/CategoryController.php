<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use Framework\Request;

class CategoryController extends Controller
{
    /**
     * List all categories.
     *
     * Pass shallow=1 to omit eager-loading products (lighter for admin lists).
     */
    public function index(Request $request)
    {
        try {
            $shallow = filter_var($request->input('shallow'), FILTER_VALIDATE_BOOLEAN);
            $with = ['children'];
            if (!$shallow) {
                $with[] = 'products';
            }

            $categories = Category::with($with)->get();

            return $this->success([
                'items' => $categories,
                'count' => count($categories),
            ], 'Categories retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve categories', ['error' => $e->getMessage()], 400);
        }
    }

    /**
     * EAV attributes linked to this category (category_attributes pivot + attribute rows).
     */
    public function linkedAttributes(Request $request)
    {
        try {
            $categoryId = $request->param('id');
            if (!$categoryId) {
                return $this->error('Category ID is required', null, 400);
            }

            $category = Category::find($categoryId);
            if (!$category) {
                return $this->error('Category not found', null, 404);
            }

            $attributes = $category->attributes()->with('options')->orderByPivot('sort_order')->get();

            return $this->success($attributes, 'Category attributes retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Replace category ↔ attribute links. Body: { "attributes": [ { "attribute_id": 1, "sort_order": 0 }, ... ] }.
     */
    public function syncAttributes(Request $request)
    {
        try {
            $categoryId = $request->param('id');
            $data = $request->json();
            if (!$categoryId) {
                return $this->error('Category ID is required', null, 400);
            }

            $category = Category::find($categoryId);
            if (!$category) {
                return $this->error('Category not found', null, 404);
            }

            $rows = $data['attributes'] ?? null;
            if (!\is_array($rows)) {
                return $this->error('attributes must be an array', null, 400);
            }

            $sync = [];
            foreach ($rows as $row) {
                $aid = $row['attribute_id'] ?? $row['id'] ?? null;
                if (null === $aid || '' === $aid) {
                    continue;
                }
                $sync[(int) $aid] = ['sort_order' => (int) ($row['sort_order'] ?? 0)];
            }

            $category->attributes()->sync($sync);
            $attributes = $category->attributes()->with('options')->orderByPivot('sort_order')->get();

            return $this->success($attributes, 'Category attributes updated successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
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
                return $this->error('Category ID is required', null, 400);
            }

            $category = Category::with('parent', 'children', 'products', 'attributes')
                ->find($categoryId)
            ;

            if (!$category) {
                return $this->error('Category not found', null, 404);
            }

            return $this->success($category, 'Category retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Create a new category.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->json();

            if (!$data || !isset($data['name'])) {
                return $this->error('Name is required', null, 400);
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

            return $this->success($category, 'Category created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Update a category.
     */
    public function update(Request $request)
    {
        try {
            $categoryId = $request->param('id');
            $data = $request->json();

            if (!$categoryId) {
                return $this->error('Category ID is required', null, 400);
            }

            $category = Category::find($categoryId);

            if (!$category) {
                return $this->error('Category not found', null, 404);
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

            return $this->success($category, 'Category updated successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
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
                return $this->error('Category ID is required', null, 400);
            }

            $category = Category::find($categoryId);

            if (!$category) {
                return $this->error('Category not found', null, 404);
            }

            $category->delete();

            return $this->success(null, 'Category deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
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
