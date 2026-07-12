<?php

namespace Modules\CrmAuth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Plans\Models\Plan;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'slug', 'email', 'phone',
        'plan_id', 'billing_cycle', 'status',
        'trial_ends_at', 'plan_starts_at', 'plan_ends_at',
    ];

    protected $casts = [
        'trial_ends_at'  => 'datetime',
        'plan_starts_at' => 'datetime',
        'plan_ends_at'   => 'datetime',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial']);
    }
}
