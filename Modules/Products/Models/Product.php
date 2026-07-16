<?php

namespace Modules\Products\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Traits\BelongsToTenant;

class Product extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'description', 'sku', 'unit_price', 'currency', 'type', 'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];
}
