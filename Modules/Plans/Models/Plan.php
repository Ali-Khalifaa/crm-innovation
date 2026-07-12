<?php

namespace Modules\Plans\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name', 'slug', 'description',
        'monthly_price', 'yearly_price',
        'max_users', 'max_contacts',
        'features', 'is_active', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'features'    => 'array',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'monthly_price' => 'float',
        'yearly_price'  => 'float',
    ];

    public function tenants(): HasMany
    {
        return $this->hasMany(\Modules\CrmAuth\Models\Tenant::class);
    }
}
