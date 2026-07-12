<?php

namespace Modules\Tenants\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Helpers\ApiResponse;
use Modules\Plans\Models\Plan;
use Modules\Tenants\Http\Resources\TenantResource;
use Modules\CrmAuth\Models\Tenant;

class TenantController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenants = Tenant::with('plan')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15);

        return ApiResponse::paginated($tenants, TenantResource::class);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'slug'          => 'required|string|unique:tenants,slug',
            'email'         => 'required|email|unique:tenants,email',
            'phone'         => 'nullable|string',
            'plan_id'       => 'required|integer|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly,custom',
            'status'        => 'required|in:trial,active,suspended,cancelled',
        ]);

        $tenant = Tenant::create($data);
        $tenant->load('plan');

        return ApiResponse::success(new TenantResource($tenant), __('crm.created'), 201);
    }

    public function show(Tenant $tenant): JsonResponse
    {
        $tenant->load('plan');

        return ApiResponse::success(new TenantResource($tenant));
    }

    public function update(Request $request, Tenant $tenant): JsonResponse
    {
        $data = $request->validate([
            'name'          => 'sometimes|string|max:150',
            'email'         => 'sometimes|email|unique:tenants,email,' . $tenant->id,
            'phone'         => 'nullable|string',
            'plan_id'       => 'sometimes|integer|exists:plans,id',
            'billing_cycle' => 'sometimes|in:monthly,yearly,custom',
            'status'        => 'sometimes|in:trial,active,suspended,cancelled',
            'plan_ends_at'  => 'nullable|date',
        ]);

        $tenant->update($data);
        $tenant->load('plan');

        return ApiResponse::success(new TenantResource($tenant));
    }

    public function destroy(Tenant $tenant): JsonResponse
    {
        $tenant->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }

    public function subscription(Tenant $tenant): JsonResponse
    {
        $tenant->load('plan');

        return ApiResponse::success([
            'tenant' => new TenantResource($tenant),
            'plan'   => new \Modules\Plans\Http\Resources\PlanResource($tenant->plan),
        ]);
    }

    public function updateSubscription(Request $request, Tenant $tenant): JsonResponse
    {
        $data = $request->validate([
            'plan_id'       => 'required|integer|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly,custom',
            'status'        => 'required|in:trial,active,suspended,cancelled',
            'plan_starts_at' => 'nullable|date',
            'plan_ends_at'   => 'nullable|date',
        ]);

        $tenant->update($data);
        $tenant->load('plan');

        return ApiResponse::success(new TenantResource($tenant), __('crm.subscription_updated'));
    }
}
