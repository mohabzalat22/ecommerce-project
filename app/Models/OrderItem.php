<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'image_url',
        'unit_price_cents',
        'quantity',
        'size_label',
        'color_label',
        'line_total_cents',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'unit_price_cents' => 'integer',
        'quantity' => 'integer',
        'line_total_cents' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
