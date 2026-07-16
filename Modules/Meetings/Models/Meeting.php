<?php

namespace Modules\Meetings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Core\Traits\BelongsToTenant;
use Modules\CrmAuth\Models\User;

class Meeting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'title', 'description', 'scheduled_at', 'duration_minutes',
        'location', 'outcome', 'status', 'meetable_type', 'meetable_id', 'assigned_to',
    ];

    protected $casts = [
        'scheduled_at'    => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function meetable(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
