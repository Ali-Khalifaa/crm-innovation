<?php

namespace Modules\Core\Traits;

trait BelongsToTenant
{
    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (auth('tenant_api')->check()) {
                $query->where('tenant_id', auth('tenant_api')->user()->tenant_id);
            }
        });

        static::creating(function ($model) {
            if (auth('tenant_api')->check() && empty($model->tenant_id)) {
                $model->tenant_id = auth('tenant_api')->user()->tenant_id;
            }
        });
    }
}
