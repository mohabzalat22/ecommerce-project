<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EavProductValue extends Model
{
    protected $table = 'eav_product_values';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'attribute_id',
        'option_id',
        'value_varchar',
        'value_text',
        'value_int',
        'value_decimal',
        'value_datetime',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'attribute_id' => 'integer',
        'option_id' => 'integer',
        'value_int' => 'integer',
        'value_decimal' => 'decimal:4',
        'value_datetime' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(EavAttribute::class, 'attribute_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(EavAttributeOption::class, 'option_id');
    }
}
