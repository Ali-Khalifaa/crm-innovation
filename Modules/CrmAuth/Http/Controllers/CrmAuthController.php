<?php

namespace Modules\CrmAuth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Helpers\ApiResponse;
use Modules\CrmAuth\Http\Requests\LoginRequest;
use Modules\CrmAuth\Http\Requests\RegisterRequest;
use Modules\CrmAuth\Http\Resources\TenantResource;
use Modules\CrmAuth\Http\Resources\UserResource;
use Modules\CrmAuth\Services\RegisterService;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CrmAuthController extends Controller
{
    public function __construct(private readonly RegisterService $registerService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->registerService->register($request->validated());

        return ApiResponse::success([
            'token'  => $result['token'],
            'user'   => new UserResource($result['user']),
            'tenant' => new TenantResource($result['tenant']),
        ], __('crm.register_success'), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $token = Auth::guard('tenant_api')->attempt($credentials);

        if (! $token) {
            return ApiResponse::error(__('crm.invalid_credentials'), 401);
        }

        $user = Auth::guard('tenant_api')->user();

        if ($user->status !== 'active') {
            Auth::guard('tenant_api')->logout();
            return ApiResponse::error(__('crm.account_inactive'), 403);
        }

        if (! $user->tenant->isActive()) {
            Auth::guard('tenant_api')->logout();
            return ApiResponse::error(__('crm.tenant_inactive'), 403);
        }

        $user->load('tenant.plan');

        return ApiResponse::success($this->respondWithToken($token, $user));
    }

    public function logout(): JsonResponse
    {
        Auth::guard('tenant_api')->logout();

        return ApiResponse::success(null, __('crm.logout_success'));
    }

    public function me(): JsonResponse
    {
        $user = Auth::guard('tenant_api')->user();
        $user->load('tenant.plan');

        return ApiResponse::success(new UserResource($user));
    }

    public function refresh(): JsonResponse
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
            $user = Auth::guard('tenant_api')->user();
            $user->load('tenant.plan');

            return ApiResponse::success($this->respondWithToken($newToken, $user));
        } catch (TokenExpiredException $e) {
            return ApiResponse::error(__('crm.token_expired'), 401);
        } catch (JWTException $e) {
            return ApiResponse::error(__('crm.token_invalid'), 401);
        }
    }

    private function respondWithToken(string $token, $user): array
    {
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => config('jwt.ttl') * 60,
            'user'         => new UserResource($user),
            'tenant'       => new TenantResource($user->tenant),
        ];
    }
}
