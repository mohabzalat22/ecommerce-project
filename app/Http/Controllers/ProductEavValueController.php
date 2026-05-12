<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\EavAttribute;
use App\Models\EavAttributeOption;
use App\Models\EavProductValue;
use App\Models\Product;
use Framework\Request;
use Illuminate\Database\Capsule\Manager as Capsule;

class ProductEavValueController extends Controller
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

            $rows = EavProductValue::query()
                ->where('product_id', $productId)
                ->with(['attribute', 'option'])
                ->get()
            ;

            return $this->success($rows, 'Product attribute values retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * Replace all EAV rows for a product. Body: { "values": [ { "attribute_id", "option_id"?, "value_varchar"?, ... } ] }.
     */
    public function sync(Request $request)
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

            $values = $data['values'] ?? null;
            if (!\is_array($values)) {
                return $this->error('values must be an array', null, 400);
            }

            $rows = [];
            foreach ($values as $row) {
                if (!isset($row['attribute_id'])) {
                    return $this->error('Each value entry requires attribute_id', null, 400);
                }

                $attributeId = (int) $row['attribute_id'];
                $attr = EavAttribute::find($attributeId);
                if (!$attr) {
                    return $this->error("Unknown attribute_id: {$attributeId}", null, 400);
                }

                $payload = [
                    'product_id' => (int) $productId,
                    'attribute_id' => $attributeId,
                    'option_id' => null,
                    'value_varchar' => null,
                    'value_text' => null,
                    'value_int' => null,
                    'value_decimal' => null,
                    'value_datetime' => null,
                ];

                $type = $attr->type;
                if ('select' === $type) {
                    if (empty($row['option_id'])) {
                        return $this->error("option_id required for select attribute {$attr->code}", null, 400);
                    }
                    $optionId = (int) $row['option_id'];
                    $ok = EavAttributeOption::query()
                        ->where('attribute_id', $attributeId)
                        ->where('id', $optionId)
                        ->exists()
                    ;
                    if (!$ok) {
                        return $this->error("Invalid option_id {$optionId} for attribute {$attr->code}", null, 400);
                    }
                    $payload['option_id'] = $optionId;
                } elseif ('text' === $type) {
                    $payload['value_text'] = $row['value_text'] ?? null;
                } elseif ('varchar' === $type) {
                    $payload['value_varchar'] = $row['value_varchar'] ?? null;
                } elseif ('int' === $type) {
                    $payload['value_int'] = isset($row['value_int']) ? (int) $row['value_int'] : null;
                } elseif ('decimal' === $type) {
                    $payload['value_decimal'] = $row['value_decimal'] ?? null;
                } elseif ('datetime' === $type) {
                    $payload['value_datetime'] = $row['value_datetime'] ?? null;
                } else {
                    $payload['value_varchar'] = $row['value_varchar'] ?? $row['value_text'] ?? null;
                }

                $rows[] = $payload;
            }

            Capsule::connection()->transaction(function () use ($productId, $rows): void {
                EavProductValue::query()->where('product_id', $productId)->delete();
                foreach ($rows as $payload) {
                    EavProductValue::create($payload);
                }
            });

            $fresh = EavProductValue::query()
                ->where('product_id', $productId)
                ->with(['attribute', 'option'])
                ->get()
            ;

            return $this->success($fresh, 'Product attribute values saved', 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }
}
