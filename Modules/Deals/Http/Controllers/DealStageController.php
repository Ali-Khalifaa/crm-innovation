<?php

namespace Modules\Deals\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Helpers\ApiResponse;
use Modules\Deals\Http\Resources\DealStageResource;
use Modules\Deals\Models\DealStage;

class DealStageController extends Controller
{
    public function index(): JsonResponse
    {
        $stages = DealStage::withCount('deals')->orderBy('order')->get();

        return ApiResponse::success(DealStageResource::collection($stages));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'color' => 'nullable|string|max:20',
            'order' => 'nullable|integer|min:0',
        ]);

        $stage = DealStage::create($data);

        return ApiResponse::success(new DealStageResource($stage), __('crm.created'), 201);
    }

    public function show(DealStage $dealStage): JsonResponse
    {
        return ApiResponse::success(new DealStageResource($dealStage));
    }

    public function update(Request $request, DealStage $dealStage): JsonResponse
    {
        $data = $request->validate([
            'name'  => 'sometimes|string|max:100',
            'color' => 'nullable|string|max:20',
            'order' => 'nullable|integer|min:0',
        ]);

        $dealStage->update($data);

        return ApiResponse::success(new DealStageResource($dealStage));
    }

    public function destroy(DealStage $dealStage): JsonResponse
    {
        if ($dealStage->deals()->exists()) {
            return ApiResponse::error(__('crm.stage_has_deals'), 422);
        }

        $dealStage->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }
}
