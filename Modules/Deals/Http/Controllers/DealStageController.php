<?php

namespace Modules\Deals\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Helpers\ApiResponse;
use Modules\Deals\Http\Requests\DealStageRequest;
use Modules\Deals\Http\Resources\DealStageResource;
use Modules\Deals\Models\DealStage;

class DealStageController extends Controller
{
    public function index(): JsonResponse
    {
        $stages = DealStage::withCount('deals')->orderBy('order')->get();

        return ApiResponse::success(DealStageResource::collection($stages));
    }

    public function store(DealStageRequest $request): JsonResponse
    {
        $stage = DealStage::create($request->validated());

        return ApiResponse::success(new DealStageResource($stage), __('crm.created'), 201);
    }

    public function show(DealStage $dealStage): JsonResponse
    {
        return ApiResponse::success(new DealStageResource($dealStage));
    }

    public function update(DealStageRequest $request, DealStage $dealStage): JsonResponse
    {
        $dealStage->update($request->validated());

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

    public function reorder(Request $request): JsonResponse
    {
        $request->validate(['stages' => 'required|array', 'stages.*.id' => 'required|integer', 'stages.*.order' => 'required|integer']);

        DB::transaction(function () use ($request) {
            foreach ($request->stages as $item) {
                DealStage::where('id', $item['id'])->update(['order' => $item['order']]);
            }
        });

        return ApiResponse::success(null, __('crm.updated'));
    }
}
