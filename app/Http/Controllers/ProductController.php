<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\EavAttribute;
use App\Models\EavAttributeOption;
use App\Models\Product;
use Framework\Request;

class ProductController extends Controller
{
    /**
     * List all products with optional filtering.
     *
     * Attribute filters (filterable EAV "select" attributes only): pass a nested query string
     * like attributes[color][]=Navy&attributes[size][]=M, or comma-separated values in one key
     * like attributes[color]=Navy,Olive. Values may be option id, option label, or option value
     * (label/value matching is case-insensitive). Multiple values for one attribute are OR;
     * different attributes are AND.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with([
                'category',
                'images' => function ($query): void {
                    $query->orderByDesc('is_primary')->orderBy('sort_order');
                },
                'attributeValues.attribute',
                'attributeValues.option',
            ]);

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

            $attributeFilters = $this->normalizeAttributeFiltersFromRequest($request);
            if ([] !== $attributeFilters) {
                $this->applyAttributeFilters($query, $attributeFilters);
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

            $product = Product::with([
                'category',
                'images' => function ($query): void {
                    $query->orderByDesc('is_primary')->orderBy('sort_order');
                },
                'attributeValues.attribute',
                'attributeValues.option',
            ])
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
     * Products in the same category as the given product (for PDP).
     */
    public function related(Request $request)
    {
        try {
            $productId = $request->param('id');

            if (!$productId) {
                return $this->error('Product ID is required', null, 400);
            }

            $product = Product::query()->find($productId);

            if (!$product) {
                return $this->error('Product not found', null, 404);
            }

            $related = Product::with([
                'images' => function ($query): void {
                    $query->orderByDesc('is_primary')->orderBy('sort_order');
                },
            ])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->orderBy('id')
                ->limit(4)
                ->get()
            ;

            return $this->success($related, 'Related products retrieved successfully', 200);
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

    /**
     * @return array<string, list<string>>
     */
    private function normalizeAttributeFiltersFromRequest(Request $request): array
    {
        $raw = $request->input('attributes');
        if (!\is_array($raw)) {
            return [];
        }

        $out = [];
        foreach ($raw as $code => $vals) {
            $code = (string) $code;
            if (!preg_match('/^[a-z][a-z0-9_]{0,63}$/', $code)) {
                continue;
            }

            if (\is_string($vals)) {
                $pieces = array_map('trim', explode(',', $vals));
            } elseif (\is_array($vals)) {
                $pieces = [];
                foreach ($vals as $v) {
                    if (\is_string($v) || is_numeric($v)) {
                        $pieces[] = trim((string) $v);
                    }
                }
            } else {
                $pieces = [trim((string) $vals)];
            }

            $pieces = array_values(array_filter($pieces, static fn (string $v): bool => '' !== $v));
            if ([] !== $pieces) {
                $out[$code] = $pieces;
            }
        }

        return $out;
    }

    /**
     * @param array<string, list<string>> $filters
     */
    private function applyAttributeFilters($query, array $filters): void
    {
        foreach ($filters as $code => $tokens) {
            $optionIds = $this->resolveOptionIdsForFilterableSelect($code, $tokens);
            if ([] === $optionIds) {
                $query->whereRaw('1 = 0');

                return;
            }

            $query->whereHas('attributeValues', static function ($q) use ($optionIds): void {
                $q->whereIn('option_id', $optionIds);
            });
        }
    }

    /**
     * @param list<string> $tokens
     *
     * @return list<int>
     */
    private function resolveOptionIdsForFilterableSelect(string $attributeCode, array $tokens): array
    {
        $attr = EavAttribute::query()
            ->where('code', $attributeCode)
            ->where('is_filterable', true)
            ->where('type', 'select')
            ->first()
        ;

        if (!$attr) {
            return [];
        }

        $ids = [];
        foreach ($tokens as $t) {
            if (ctype_digit($t)) {
                $id = (int) $t;
                $exists = EavAttributeOption::query()
                    ->where('attribute_id', $attr->id)
                    ->where('id', $id)
                    ->exists()
                ;
                if ($exists) {
                    $ids[] = $id;
                }

                continue;
            }

            $lower = strtolower($t);
            $opt = EavAttributeOption::query()
                ->where('attribute_id', $attr->id)
                ->where(function ($q) use ($lower): void {
                    $q->whereRaw('LOWER(label) = ?', [$lower])
                        ->orWhereRaw('LOWER(value) = ?', [$lower])
                    ;
                })
                ->first()
            ;

            if ($opt) {
                $ids[] = (int) $opt->id;
            }
        }

        return array_values(array_unique($ids));
    }
}
