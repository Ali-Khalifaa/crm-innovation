<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Helpers\ApiResponse;

class CheckPlanLimit
{
    public function handle(Request $request, Closure $next, string $feature): mixed
    {
        $user   = auth('tenant_api')->user();
        $tenant = $user->tenant;
        $plan   = $tenant->plan;

        if (! $plan) {
            return ApiResponse::error(__('crm.no_active_plan'), 403);
        }

        $limit = match ($feature) {
            'contacts' => $plan->max_contacts,
            'users'    => $plan->max_users,
            default    => 0,
        };

        if ($limit > 0) {
            $count = match ($feature) {
                'contacts' => \Modules\Contacts\Models\Contact::withoutGlobalScope('tenant')
                    ->where('tenant_id', $tenant->id)->count(),
                'users' => \Modules\CrmAuth\Models\User::withoutGlobalScope('tenant')
                    ->where('tenant_id', $tenant->id)->count(),
                default => 0,
            };

            if ($count >= $limit) {
                return ApiResponse::error(__('crm.plan_limit_reached'), 403);
            }
        }

        $features = $plan->features ?? [];
        $featureMap = [
            'invoices' => 'invoices',
            'reports'  => 'reports',
        ];

        if (isset($featureMap[$feature]) && ! in_array($featureMap[$feature], $features)) {
            return ApiResponse::error(__('crm.feature_not_available'), 403);
        }

        return $next($request);
    }
}
