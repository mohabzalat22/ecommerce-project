<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Framework\Request;
use Illuminate\Database\Capsule\Manager as Capsule;

class OrderController extends Controller
{
    private const SHIPPING_CENTS = 599;

    private static function generateUuidV4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
        $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);

        return vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(bin2hex($bytes), 4),
        );
    }

    /**
     * Create a storefront order (UUID id generated server-side).
     *
     * JSON body:
     * {
     *   "shipping": { "email", "full_name", "address_line1", "city", "postal_code" },
     *   "lines": [{ "product_id", "name", "image_url", "unit_price_cents", "quantity", "size_label", "color_label" }]
     * }
     */
    public function store(Request $request)
    {
        try {
            $data = $request->json();
            $shipping = $data['shipping'] ?? null;
            $lines = $data['lines'] ?? null;

            if (!is_array($shipping) || !is_array($lines)) {
                return $this->error('Invalid request body', null, 400);
            }

            $errors = $this->validateShipping($shipping);
            if ([] !== $errors) {
                return $this->error('Validation failed', $errors, 422);
            }

            if ([] === $lines) {
                return $this->error('Order must include at least one line', ['lines' => 'Lines cannot be empty'], 422);
            }

            $errors = [];
            $normalized = [];
            foreach ($lines as $i => $line) {
                if (!is_array($line)) {
                    $errors["lines.{$i}"] = 'Each line must be an object';

                    continue;
                }
                $lineErrors = $this->validateLine($line);
                foreach ($lineErrors as $k => $msg) {
                    $errors["lines.{$i}.{$k}"] = $msg;
                }
                if ([] === $lineErrors) {
                    $normalized[] = $line;
                }
            }

            if ([] !== $errors) {
                return $this->error('Validation failed', $errors, 422);
            }

            $productIds = array_map(static fn (array $l): int => (int) $l['product_id'], $normalized);
            $existingIds = Product::query()->whereIn('id', $productIds)->pluck('id')->all();
            $missing = array_diff($productIds, $existingIds);
            if ([] !== $missing) {
                return $this->error(
                    'One or more products were not found',
                    ['product_ids' => 'Unknown product id(s): '.implode(', ', $missing)],
                    422,
                );
            }

            $subtotalCents = 0;
            foreach ($normalized as $line) {
                $qty = (int) $line['quantity'];
                $unit = (int) $line['unit_price_cents'];
                $subtotalCents += $unit * $qty;
            }

            $shippingCents = self::SHIPPING_CENTS;
            $totalCents = $subtotalCents + $shippingCents;

            $orderId = self::generateUuidV4();

            $orderPayload = [
                'id' => $orderId,
                'email' => trim((string) $shipping['email']),
                'full_name' => trim((string) $shipping['full_name']),
                'address_line1' => trim((string) $shipping['address_line1']),
                'city' => trim((string) $shipping['city']),
                'postal_code' => trim((string) $shipping['postal_code']),
                'subtotal_cents' => $subtotalCents,
                'shipping_cents' => $shippingCents,
                'total_cents' => $totalCents,
            ];

            Capsule::connection()->transaction(function () use ($orderPayload, $normalized): void {
                Order::query()->create($orderPayload);

                foreach ($normalized as $line) {
                    $qty = (int) $line['quantity'];
                    $unit = (int) $line['unit_price_cents'];
                    OrderItem::query()->create([
                        'order_id' => $orderPayload['id'],
                        'product_id' => (int) $line['product_id'],
                        'name' => (string) $line['name'],
                        'image_url' => (string) $line['image_url'],
                        'unit_price_cents' => $unit,
                        'quantity' => $qty,
                        'size_label' => (string) $line['size_label'],
                        'color_label' => (string) $line['color_label'],
                        'line_total_cents' => $unit * $qty,
                    ]);
                }
            });

            $response = [
                'id' => $orderId,
                'subtotal_cents' => $subtotalCents,
                'shipping_cents' => $shippingCents,
                'total_cents' => $totalCents,
            ];

            return $this->success($response, 'Order placed successfully', 201);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 500);
        }
    }

    /**
     * @return array<string, string>
     */
    private function validateShipping(array $shipping): array
    {
        $errors = [];
        $email = isset($shipping['email']) ? trim((string) $shipping['email']) : '';
        if ('' === $email) {
            $errors['shipping.email'] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['shipping.email'] = 'Enter a valid email.';
        }

        foreach (['full_name' => 'Full name', 'address_line1' => 'Address', 'city' => 'City', 'postal_code' => 'Postal code'] as $key => $label) {
            $val = isset($shipping[$key]) ? trim((string) $shipping[$key]) : '';
            if ('' === $val) {
                $errors['shipping.'.$key] = $label.' is required.';
            }
        }

        return $errors;
    }

    /**
     * @return array<string, string>
     */
    private function validateLine(array $line): array
    {
        $errors = [];
        if (!isset($line['product_id']) || !is_numeric($line['product_id']) || (int) $line['product_id'] < 1) {
            $errors['product_id'] = 'Valid product_id is required.';
        }
        foreach (['name', 'image_url', 'size_label', 'color_label'] as $key) {
            if (!isset($line[$key]) || '' === trim((string) $line[$key])) {
                $errors[$key] = 'Required.';
            }
        }
        if (!isset($line['unit_price_cents']) || !is_numeric($line['unit_price_cents']) || (int) $line['unit_price_cents'] < 0) {
            $errors['unit_price_cents'] = 'Must be a non-negative integer (cents).';
        }
        if (!isset($line['quantity']) || !is_numeric($line['quantity']) || (int) $line['quantity'] < 1) {
            $errors['quantity'] = 'Must be at least 1.';
        }

        return $errors;
    }
}
