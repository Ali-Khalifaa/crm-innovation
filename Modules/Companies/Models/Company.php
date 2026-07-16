<?php

namespace Modules\Companies\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\BelongsToTenant;
use Modules\CrmAuth\Models\User;

class Company extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'industry', 'website', 'phone', 'email',
        'address', 'employees_count', 'annual_revenue', 'status', 'notes', 'created_by',
    ];

    protected $casts = [
        'annual_revenue'  => 'decimal:2',
        'employees_count' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(\Modules\Contacts\Models\Contact::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(\Modules\Deals\Models\Deal::class);
    }
}
