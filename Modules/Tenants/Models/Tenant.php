<?php

namespace Modules\Tenants\Models;

/**
 * Alias to CrmAuth\Tenant — single source of truth for the tenants table.
 * All CRM logic lives in Modules\CrmAuth\Models\Tenant.
 */
class Tenant extends \Modules\CrmAuth\Models\Tenant
{
    //
}
