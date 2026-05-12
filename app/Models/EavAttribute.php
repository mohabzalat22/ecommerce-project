<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Category;
use App\Models\EavAttributeOption;
use App\Models\EavProductValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EavAttribute extends Model
{
    protected $table = 'eav_attributes';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'type',
        'is_required',
        'is_filterable',
        'is_searchable',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'is_searchable' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(EavAttributeOption::class, 'attribute_id');
    }

    public function productValues(): HasMany
    {
        return $this->hasMany(EavProductValue::class, 'attribute_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_attributes', 'attribute_id', 'category_id')
            ->withPivot('sort_order');
    }
}
