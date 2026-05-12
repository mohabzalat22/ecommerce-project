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

            return json_encode([
                'success' => true,
                'data' => $products,
                'count' => count($products),
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
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
                return json_encode([
                    'success' => false,
                    'message' => 'Product ID is required',
                ]);
            }

            $product = Product::with('category', 'images', 'attributeValues')
                ->find($productId)
            ;

            if (!$product) {
                return json_encode([
                    'success' => false,
                    'message' => 'Product not found',
                ]);
            }

            return json_encode([
                'success' => true,
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
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
                return json_encode([
                    'success' => false,
                    'message' => 'Name and category_id are required',
                ]);
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

            return json_encode([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
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
                return json_encode([
                    'success' => false,
                    'message' => 'Product ID is required',
                ]);
            }

            $product = Product::find($productId);

            if (!$product) {
                return json_encode([
                    'success' => false,
                    'message' => 'Product not found',
                ]);
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

            return json_encode([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
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
                return json_encode([
                    'success' => false,
                    'message' => 'Product ID is required',
                ]);
            }

            $product = Product::find($productId);

            if (!$product) {
                return json_encode([
                    'success' => false,
                    'message' => 'Product not found',
                ]);
            }

            $product->delete();

            return json_encode([
                'success' => true,
                'message' => 'Product deleted successfully',
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
