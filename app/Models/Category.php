<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\EavAttribute;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';

    public $timestamps = false;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'image_url',
        'description',
        'is_active',
        'sort_order',
        'created_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(EavAttribute::class, 'category_attributes', 'category_id', 'attribute_id')
            ->withPivot('sort_order');
    }
}