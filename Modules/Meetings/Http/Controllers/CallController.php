<?php

namespace Modules\Meetings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Contacts\Models\Contact;
use Modules\Core\Helpers\ApiResponse;
use Modules\Deals\Models\Deal;
use Modules\Meetings\Http\Requests\StoreCallRequest;
use Modules\Meetings\Http\Resources\CallResource;
use Modules\Meetings\Models\Call;

class CallController extends Controller
{
    private function morphClass(string $type): string
    {
        return match ($type) {
            'contact' => Contact::class,
            'deal'    => Deal::class,
            default   => $type,
        };
    }

    public function index(): JsonResponse
    {
        $query = Call::with(['user']);

        if ($type = request('callable_type')) {
            $query->where('callable_type', $this->morphClass($type));
        }
        if ($id = request('callable_id')) {
            $query->where('callable_id', $id);
        }
        if ($outcome = request('outcome')) {
            $query->where('outcome', $outcome);
        }

        $calls = $query->orderBy('called_at', 'desc')->paginate(20);

        return ApiResponse::paginated($calls, CallResource::class);
    }

    public function store(StoreCallRequest $request): JsonResponse
    {
        $data = $request->validated();

        $callableClass = $this->morphClass($data['callable_type']);
        $callable = $callableClass::findOrFail($data['callable_id']);

        $call = Call::create([
            ...$data,
            'callable_type' => $callable::class,
            'callable_id'   => $callable->id,
            'user_id'       => Auth::guard('tenant_api')->id(),
        ]);

        $call->load('user');

        return ApiResponse::success(new CallResource($call), __('crm.created'), 201);
    }

    public function show(Call $call): JsonResponse
    {
        $call->load(['user', 'callable']);

        return ApiResponse::success(new CallResource($call));
    }

    public function destroy(Call $call): JsonResponse
    {
        $call->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }
}
