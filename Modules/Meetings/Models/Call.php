<?php

namespace Modules\Meetings\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Core\Traits\BelongsToTenant;
use Modules\CrmAuth\Models\User;

class Call extends Model
{
    use BelongsToTenant;

    protected $table = 'calls';

    protected $fillable = [
        'tenant_id', 'direction', 'duration_seconds', 'outcome',
        'notes', 'called_at', 'callable_type', 'callable_id', 'user_id',
    ];

    protected $casts = [
        'called_at'        => 'datetime',
        'duration_seconds' => 'integer',
    ];

    public function callable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
