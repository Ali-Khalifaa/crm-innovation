<?php

namespace Modules\Subscriptions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CrmAuth\Models\Tenant;
use Modules\Plans\Models\Plan;

class PlanChangeRequest extends Model
{
    protected $fillable = [
        'tenant_id', 'requested_plan_id', 'message', 'status',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function requestedPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'requested_plan_id');
    }
}
