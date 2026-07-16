<?php

namespace Modules\Deals\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Contacts\Models\Contact;
use Modules\Core\Traits\BelongsToTenant;
use Modules\CrmAuth\Models\User;

class Deal extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'title', 'contact_id', 'stage_id',
        'value', 'currency', 'probability', 'expected_close_date',
        'assigned_to', 'status', 'notes',
        'lost_reason', 'closed_at', 'company_id',
    ];

    protected $casts = [
        'expected_close_date' => 'date',
        'closed_at'           => 'datetime',
        'value'               => 'float',
    ];

    public function stage(): BelongsTo
    {
        return $this->belongsTo(DealStage::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(\Modules\Tasks\Models\Task::class, 'taskable_id')
            ->where('taskable_type', self::class);
    }
}
