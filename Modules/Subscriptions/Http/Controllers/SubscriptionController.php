<?php

namespace Modules\Subscriptions\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Helpers\ApiResponse;
use Modules\CrmAuth\Http\Resources\TenantResource;
use Modules\Plans\Http\Resources\PlanResource;
use Modules\Plans\Models\Plan;
use Modules\Subscriptions\Models\PlanChangeRequest;

class SubscriptionController extends Controller
{
    public function current(): JsonResponse
    {
        $user   = Auth::guard('tenant_api')->user();
        $tenant = $user->tenant->load('plan');

        return ApiResponse::success([
            'tenant' => new TenantResource($tenant),
            'plan'   => new PlanResource($tenant->plan),
            'status' => $tenant->status,
            'trial_ends_at'  => $tenant->trial_ends_at,
            'plan_ends_at'   => $tenant->plan_ends_at,
        ]);
    }

    public function requestUpgrade(Request $request): JsonResponse
    {
        $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
            'message' => 'nullable|string|max:500',
        ]);

        $user   = Auth::guard('tenant_api')->user();
        $tenant = $user->tenant;

        if ($tenant->plan_id === (int) $request->plan_id) {
            return ApiResponse::error(__('crm.already_on_plan'), 422);
        }

        $pending = PlanChangeRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
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
