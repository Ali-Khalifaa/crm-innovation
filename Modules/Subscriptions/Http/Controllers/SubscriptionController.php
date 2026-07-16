<?php

namespace Modules\Subscriptions\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Helpers\ApiResponse;
use Modules\CrmAuth\Http\Resources\TenantResource;
use Modules\Plans\Http\Resources\PlanResource;
use Modules\Subscriptions\Http\Requests\UpgradeRequest;
use Modules\Subscriptions\Models\PlanChangeRequest;

class SubscriptionController extends Controller
{
    public function current(): JsonResponse
    {
        $user   = Auth::guard('tenant_api')->user();
        $tenant = $user->tenant->load('plan');

        return ApiResponse::success([
            'tenant'        => new TenantResource($tenant),
            'plan'          => new PlanResource($tenant->plan),
            'status'        => $tenant->status,
            'trial_ends_at' => $tenant->trial_ends_at,
            'plan_ends_at'  => $tenant->plan_ends_at,
        ]);
    }

    public function requestUpgrade(UpgradeRequest $request): JsonResponse
    {
        $user   = Auth::guard('tenant_api')->user();
        $tenant = $user->tenant;

        if ($tenant->plan_id === (int) $request->plan_id) {
            return ApiResponse::error(__('crm.already_on_plan'), 422);
        }

        if (PlanChangeRequest::where('tenant_id', $tenant->id)->where('status', 'pending')->exists()) {
            return ApiResponse::error(__('crm.upgrade_request_pending'), 422);
        }

        PlanChangeRequest::create([
            'tenant_id'         => $tenant->id,
            'requested_plan_id' => $request->plan_id,
            'message'           => $request->message,
            'status'            => 'pending',
        ]);

        return ApiResponse::success(null, __('crm.upgrade_request_sent'), 201);
    }
}
