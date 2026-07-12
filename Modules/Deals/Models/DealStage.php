<?php

namespace Modules\Deals\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Traits\BelongsToTenant;

class DealStage extends Model
{
    use BelongsToTenant;

    protected $table = 'deal_stages';

    protected $fillable = [
        'tenant_id', 'name', 'color', 'order',
    ];

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'stage_id');
    }
}
