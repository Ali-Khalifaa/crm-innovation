<?php

namespace Modules\Deals\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Helpers\ApiResponse;
use Modules\Core\Models\CrmActivity;
use Modules\Deals\Http\Requests\StoreDealRequest;
use Modules\Deals\Http\Requests\UpdateDealRequest;
use Modules\Deals\Http\Resources\DealResource;
use Modules\Deals\Models\Deal;

class DealController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Deal::with(['stage', 'contact', 'assignee']);

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        if ($stageId = request('stage_id')) {
            $query->where('stage_id', $stageId);
        }

        if ($search = request('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        // Kanban: return all grouped by stage
        if (request('kanban')) {
            $deals = $query->orderBy('created_at', 'desc')->get();
            return ApiResponse::success(DealResource::collection($deals));
        }

        $deals = $query->latest()->paginate(15);

        return ApiResponse::paginated($deals, DealResource::class);
    }

    public function store(StoreDealRequest $request): JsonResponse
    {
        $deal = Deal::create($request->validated());
        $deal->load(['stage', 'contact']);
        CrmActivity::log('deal_created', $deal, "Created deal: {$deal->title} ($" . number_format($deal->value, 2) . ")");

        return ApiResponse::success(new DealResource($deal), __('crm.created'), 201);
    }

    public function show(Deal $deal): JsonResponse
    {
        $deal->load(['stage', 'contact', 'assignee']);

        return ApiResponse::success(new DealResource($deal));
    }

    public function update(UpdateDealRequest $request, Deal $deal): JsonResponse
    {
        $oldStatus = $deal->status;
        $deal->update($request->validated());
        $deal->load(['stage', 'contact']);

        $desc = "Updated deal: {$deal->title}";
        if ($oldStatus !== $deal->status) {
            $desc .= " (status: {$oldStatus} → {$deal->status})";
        }
        CrmActivity::log('deal_updated', $deal, $desc);

        return ApiResponse::success(new DealResource($deal));
    }

    public function destroy(Deal $deal): JsonResponse
    {
        $deal->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }
}
