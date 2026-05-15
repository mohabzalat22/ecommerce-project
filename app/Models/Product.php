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

    protected $appends = ['price_after_tax'];
    private static ?bool $taxEnabledCache = null;

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

    public function getPriceAfterTaxAttribute(): float
    {
        $currentPrice = null !== $this->sale_price ? (float) $this->sale_price : (float) $this->base_price;
        if (!self::isTaxEnabled()) {
            return round($currentPrice, 2);
        }

        $taxAmount = $currentPrice * 0.14;

        return round($currentPrice + $taxAmount, 2);
    }

    private static function isTaxEnabled(): bool
    {
        if (null !== self::$taxEnabledCache) {
            return self::$taxEnabledCache;
        }

        $rawValue = TaxSetting::query()->where('key', TaxSetting::TAX_ENABLED_KEY)->value('value');
        self::$taxEnabledCache = filter_var($rawValue, FILTER_VALIDATE_BOOLEAN);

        return self::$taxEnabledCache;
    }
}
