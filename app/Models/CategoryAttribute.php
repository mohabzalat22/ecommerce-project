<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryAttribute extends Model
{
    protected $table = 'category_attributes';

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'attribute_id',
        'sort_order',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'attribute_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(EavAttribute::class, 'attribute_id');
    }
}
