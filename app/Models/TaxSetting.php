<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    public const TAX_ENABLED_KEY = 'prices.tax_enabled';

    public $timestamps = false;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
    ];
}
