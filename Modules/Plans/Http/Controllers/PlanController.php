<?php

namespace Modules\Plans\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Helpers\ApiResponse;
use Modules\Plans\Http\Requests\StorePlanRequest;
use Modules\Plans\Http\Requests\UpdatePlanRequest;
use Modules\Plans\Http\Resources\PlanResource;
use Modules\Plans\Models\Plan;

class PlanController extends Controller
{
    // Public endpoint for landing page — no auth
    public function publicIndex()
    {
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();

        return ApiResponse::success(PlanResource::collection($plans));
    }

    // Super Admin CRUD
    public function index(Request $request)
    {
        $plans = Plan::orderBy('sort_order')->paginate($request->per_page ?? 15);

        return ApiResponse::paginated($plans, PlanResource::class);
    }

    public function store(StorePlanRequest $request)
    {
        $plan = Plan::create($request->validated());

        return ApiResponse::success(new PlanResource($plan), __('crm.created'), 201);
    }

    public function show(Plan $plan)
    {
        return ApiResponse::success(new PlanResource($plan));
    }

    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        $plan->update($request->validated());

        return ApiResponse::success(new PlanResource($plan));
    }

    public function destroy(Plan $plan)
    {
        if ($plan->tenants()->exists()) {
            return ApiResponse::error(__('crm.plan_has_tenants'), 422);
        }

        $plan->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }
}
