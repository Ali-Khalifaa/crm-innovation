<?php

namespace Modules\CrmAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Helpers\ApiResponse;
use Modules\CrmAuth\Http\Resources\UserResource;
use Modules\CrmAuth\Models\Tenant;
use Modules\CrmAuth\Models\User;

class SettingsController extends Controller
{
    public function company(): JsonResponse
    {
        $user   = Auth::guard('tenant_api')->user();
        $tenant = $user->tenant->load('plan');

        return ApiResponse::success([
            'user'   => new UserResource($user),
            'tenant' => $tenant,
        ]);
    }

    public function updateCompany(Request $request): JsonResponse
    {
        $user   = Auth::guard('tenant_api')->user();
        $tenant = $user->tenant;

        if (! $user->isAdmin()) {
            return ApiResponse::error(__('crm.unauthorized'), 403);
        }

        $data = $request->validate([
            'name'  => 'sometimes|string|max:150',
            'phone' => 'nullable|string|max:30',
            'email' => 'sometimes|email|unique:tenants,email,' . $tenant->id,
        ]);

        $tenant->update($data);
        $tenant->load('plan');

        return ApiResponse::success($tenant, __('crm.updated'));
    }

    public function users(): JsonResponse
    {
        $tenantId = Auth::guard('tenant_api')->user()->tenant_id;

        $users = User::where('tenant_id', $tenantId)->get();

        return ApiResponse::success(UserResource::collection($users));
    }

    public function profile(): JsonResponse
    {
        $user = Auth::guard('tenant_api')->user();

        return ApiResponse::success(new UserResource($user));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::guard('tenant_api')->user();

        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'title' => 'nullable|string|max:100',
        ]);

        $user->update($data);

        return ApiResponse::success(new UserResource($user), __('crm.updated'));
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $user = Auth::guard('tenant_api')->user();

        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return ApiResponse::error(__('crm.invalid_current_password'), 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return ApiResponse::success(null, __('crm.password_updated'));
    }

    public function inviteUser(Request $request): JsonResponse
    {
        $authUser = Auth::guard('tenant_api')->user();

        if (! $authUser->isAdmin()) {
            return ApiResponse::error(__('crm.unauthorized'), 403);
        }

        $tenant = $authUser->tenant;
        $plan   = $tenant->plan;

        // Check user limit
        if ($plan->max_users > 0) {
            $count = User::where('tenant_id', $tenant->id)->count();
            if ($count >= $plan->max_users) {
                return ApiResponse::error(__('crm.plan_limit_reached'), 403);
            }
        }

        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:admin,member',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
            'status'    => 'active',
        ]);

        return ApiResponse::success(new UserResource($user), __('crm.user_invited'), 201);
    }
}
