<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Framework\Request;

class ProductImageController extends Controller
{
    public function index(Request $request)
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

            $images = ProductImage::query()
                ->where('product_id', $productId)
                ->orderByDesc('is_primary')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
            ;

            return $this->success($images, 'Images retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $productId = $request->param('id');
            $data = $request->json();
            if (!$productId) {
                return $this->error('Product ID is required', null, 400);
            }

            $product = Product::find($productId);
            if (!$product) {
                return $this->error('Product not found', null, 404);
            }

            if (!isset($data['image_url'])) {
                return $this->error('image_url is required', null, 400);
            }

            if (!empty($data['is_primary'])) {
                ProductImage::query()->where('product_id', $productId)->update(['is_primary' => false]);
            }

            $image = ProductImage::create([
                'product_id' => (int) $productId,
                'image_url' => $data['image_url'],
                'alt_text' => $data['alt_text'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_primary' => !empty($data['is_primary']),
            ]);

            return $this->success($image, 'Image created successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $productId = $request->param('id');
            $imageId = $request->param('imageId');
            $data = $request->json();
            if (!$productId || !$imageId) {
                return $this->error('Product ID and image ID are required', null, 400);
            }

            $image = ProductImage::query()
                ->where('product_id', $productId)
                ->where('id', $imageId)
                ->first()
            ;

            if (!$image) {
                return $this->error('Image not found', null, 404);
            }

            if (!empty($data['is_primary'])) {
                ProductImage::query()->where('product_id', $productId)->update(['is_primary' => false]);
            }

            $image->update([
                'image_url' => $data['image_url'] ?? $image->image_url,
                'alt_text' => array_key_exists('alt_text', $data) ? $data['alt_text'] : $image->alt_text,
                'sort_order' => $data['sort_order'] ?? $image->sort_order,
                'is_primary' => array_key_exists('is_primary', $data) ? (bool) $data['is_primary'] : $image->is_primary,
            ]);

            return $this->success($image->fresh(), 'Image updated successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $productId = $request->param('id');
            $imageId = $request->param('imageId');
            if (!$productId || !$imageId) {
                return $this->error('Product ID and image ID are required', null, 400);
            }

            $image = ProductImage::query()
                ->where('product_id', $productId)
                ->where('id', $imageId)
                ->first()
            ;

            if (!$image) {
                return $this->error('Image not found', null, 404);
            }

            $image->delete();

            return $this->success(null, 'Image deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }
}
