<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\CrmAuth\Models\User;

class CrmActivity extends Model
{
    protected $table = 'crm_activities';

    protected $fillable = [
        'tenant_id', 'user_id', 'subject_type', 'subject_id',
        'action', 'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public static function log(string $action, Model $subject, string $description = ''): void
    {
        $user     = auth('tenant_api')->user();
        $tenantId = $user?->tenant_id;

        if (! $tenantId) return;

        static::create([
            'tenant_id'    => $tenantId,
            'user_id'      => $user?->id,
            'subject_type' => get_class($subject),
            'subject_id'   => $subject->getKey(),
            'action'       => $action,
            'description'  => $description,
        ]);
    }
}
