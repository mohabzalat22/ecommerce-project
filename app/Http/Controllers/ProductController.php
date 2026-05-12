<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use Framework\Request;

class ProductController extends Controller
{
    /**
     * List all products with optional filtering.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with('category', 'images', 'attributeValues');

            // Filter by category if provided
            if ($request->input('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }

            // Filter by active status
            if (null !== $request->input('is_active')) {
                $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
            }

            // Search by name or sku
            if ($request->input('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                    ;
                });
            }

            $products = $query->get();

            return $this->success($products, 'Products retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Get a single product by ID.
     */
    public function show(Request $request)
    {
        try {
            $productId = $request->param('id');

            if (!$productId) {
                return $this->error('Product ID is required', null, 400);
            }

            $product = Product::with('category', 'images', 'attributeValues')
                ->find($productId)
            ;

            if (!$product) {
                return $this->error('Product not found', null, 404);
            }

            return $this->success($product, 'Product retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Create a new product.
     */
    public function store(Request $request)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            if (!$data || !isset($data['name']) || !isset($data['category_id'])) {
                return $this->error('Name and category_id are required', null, 400);
            }

            $product = Product::create([
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $data['slug'] ?? $this->generateSlug($data['name']),
                'sku' => $data['sku'] ?? null,
                'base_price' => $data['base_price'] ?? 0,
                'sale_price' => $data['sale_price'] ?? null,
                'stock_qty' => $data['stock_qty'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->success($product, 'Product created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Update a product.
     */
    public function update(Request $request)
    {
        try {
            $productId = $request->param('id');
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$productId) {
                return $this->error('Product ID is required', null, 400);
            }

            $product = Product::find($productId);

            if (!$product) {
                return $this->error('Product not found', null, 404);
            }

            $product->update([
                'category_id' => $data['category_id'] ?? $product->category_id,
                'name' => $data['name'] ?? $product->name,
                'slug' => $data['slug'] ?? $product->slug,
                'sku' => $data['sku'] ?? $product->sku,
                'base_price' => $data['base_price'] ?? $product->base_price,
                'sale_price' => $data['sale_price'] ?? $product->sale_price,
                'stock_qty' => $data['stock_qty'] ?? $product->stock_qty,
                'is_active' => $data['is_active'] ?? $product->is_active,
            ]);

            return $this->success($product, 'Product updated successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Delete a product.
     */
    public function destroy(Request $request)
    {
        try {
            $productId = $request->param('id');

            if (!$productId) {
                return $this->error('Product ID is required', null, 400);
            }

            $product = Product::find($productId);

            if (!$product) {
                return $this->error('Product not found', null, 404);
            }

            $product->delete();

            return $this->success(null, 'Product deleted successfully', 200);
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
