<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use Framework\Request;

class HomeController extends Controller
{
    public function index()
    {
        return $this->success(['message' => 'hello']);
    }

    /**
     * Categories and featured products for the storefront home page.
     */
    public function storefront(Request $request)
    {
        try {
            $categories = Category::query()
                ->where('parent_id', 1)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'slug', 'image_url', 'description'])
            ;

            $featuredProducts = Product::with([
                'images' => function ($query): void {
                    $query->orderByDesc('is_primary')->orderBy('sort_order');
                },
            ])
                ->where('is_active', true)
                ->orderByDesc('id')
                ->limit(8)
                ->get()
            ;

            return $this->success([
                'categories' => $categories,
                'featured_products' => $featuredProducts,
            ], 'Storefront home retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function store()
    {
        return $this->success([
            'message' => 'post received',
            'method' => $_SERVER['REQUEST_METHOD'] ?? null,
        ], 'Data received');
    }

    public function destroy()
    {
        return $this->success([
            'message' => 'resource deleted',
            'method' => $_SERVER['REQUEST_METHOD'] ?? null,
        ]);
    }
}
