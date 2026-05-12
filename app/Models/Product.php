<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public $timestamps = false;
    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'base_price',
        'sale_price',
        'stock_qty',
        'is_active',
        'created_at',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'base_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_qty' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(EavProductValue::class, 'product_id');
    }
}
