<?php

namespace Modules\Contacts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\BelongsToTenant;
use Modules\CrmAuth\Models\User;

class Contact extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'first_name', 'last_name', 'email', 'phone',
        'company', 'address', 'notes', 'status', 'created_by',
        'lead_source', 'owner_id', 'company_id',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function companyRecord(): BelongsTo
    {
        return $this->belongsTo(\Modules\Companies\Models\Company::class, 'company_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(\Modules\Deals\Models\Deal::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(\Modules\Tasks\Models\Task::class, 'taskable_id')
            ->where('taskable_type', self::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
