<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $table = 'product_images';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'image_url',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
