<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EavAttributeOption extends Model
{
    public $timestamps = false;
    protected $table = 'eav_attribute_options';

    protected $fillable = [
        'attribute_id',
        'label',
        'value',
        'sort_order',
    ];

    protected $casts = [
        'attribute_id' => 'integer',
        'sort_order' => 'integer',
    ];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(EavAttribute::class, 'attribute_id');
    }

    public function productValues(): HasMany
    {
        return $this->hasMany(EavProductValue::class, 'option_id');
    }
}
