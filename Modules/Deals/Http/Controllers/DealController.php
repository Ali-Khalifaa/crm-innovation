<?php

namespace Modules\Deals\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Core\Helpers\ApiResponse;
use Modules\Core\Models\CrmActivity;
use Modules\Core\Services\NotificationService;
use Modules\Deals\Http\Requests\StoreDealRequest;
use Modules\Deals\Http\Requests\UpdateDealRequest;
use Modules\Deals\Http\Resources\DealResource;
use Modules\Deals\Models\Deal;

class DealController extends Controller
{
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Deal::class);

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

        if (request('kanban')) {
            $deals = $query->orderBy('created_at', 'desc')->get();
            return ApiResponse::success(DealResource::collection($deals));
        }

        $deals = $query->latest()->paginate(15);

        return ApiResponse::paginated($deals, DealResource::class);
    }

    public function store(StoreDealRequest $request): JsonResponse
    {
        $this->authorize('create', Deal::class);

        $deal = Deal::create($request->validated());
        $deal->load(['stage', 'contact']);
        CrmActivity::log('deal_created', $deal, "Created deal: {$deal->title} ($" . number_format($deal->value, 2) . ")");

        // Notify assigned user if different from creator
        $actor = auth('tenant_api')->user();
        if ($deal->assigned_to && $deal->assigned_to !== $actor->id) {
            NotificationService::notify(
                $actor->tenant_id,
                $deal->assigned_to,
                'deal_assigned',
                __('crm.notif_deal_assigned_title', ['title' => $deal->title]),
                __('crm.notif_deal_assigned_body', ['name' => $actor->name]),
                ['url' => '/crm/deals', 'id' => $deal->id]
            );
        }

        return ApiResponse::success(new DealResource($deal), __('crm.created'), 201);
    }

    public function show(Deal $deal): JsonResponse
    {
        $this->authorize('view', $deal);

        $deal->load(['stage', 'contact', 'assignee']);

        return ApiResponse::success(new DealResource($deal));
    }

    public function update(UpdateDealRequest $request, Deal $deal): JsonResponse
    {
        $this->authorize('update', $deal);

        $oldStatus = $deal->status;
        $deal->update($request->validated());
        $deal->load(['stage', 'contact']);

        $desc = "Updated deal: {$deal->title}";
        if ($oldStatus !== $deal->status) {
            $desc .= " (status: {$oldStatus} → {$deal->status})";
        }
        CrmActivity::log('deal_updated', $deal, $desc);

        // Notify team when deal is won or lost
        $actor = auth('tenant_api')->user();
        if ($oldStatus !== $deal->status && in_array($deal->status, ['won', 'lost'])) {
            $type  = $deal->status === 'won' ? 'deal_won' : 'deal_lost';
            $title = $deal->status === 'won'
                ? __('crm.notif_deal_won_title', ['title' => $deal->title])
                : __('crm.notif_deal_lost_title', ['title' => $deal->title]);
            NotificationService::notifyTenant(
                $actor->tenant_id,
                $actor->id,
                $type,
                $title,
                __('crm.notif_deal_status_body', ['name' => $actor->name, 'value' => number_format($deal->value, 2)]),
                ['url' => '/crm/deals', 'id' => $deal->id]
            );
        }

        return ApiResponse::success(new DealResource($deal));
    }

    public function destroy(Deal $deal): JsonResponse
    {
        $this->authorize('delete', $deal);

        $deal->delete();

        return ApiResponse::success(null, __('crm.deleted'));
    }

    public function bulkAction(\Illuminate\Http\Request $request): JsonResponse
    {
        $this->authorize('viewAny', Deal::class);

        $request->validate([
            'action' => 'required|in:delete,won,lost',
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer',
        ]);

        $user = auth('tenant_api')->user();
        $ids = Deal::whereIn('id', $request->ids)
            ->where('tenant_id', $user->tenant_id)
            ->pluck('id');

        $count = $ids->count();

        if ($request->action === 'delete') {
            Deal::whereIn('id', $ids)->delete();
        } elseif (in_array($request->action, ['won', 'lost'])) {
            Deal::whereIn('id', $ids)->update([
                'status'      => $request->action,
                'probability' => $request->action === 'won' ? 100 : 0,
                'closed_at'   => now()->toDateString(),
            ]);
        }

        return ApiResponse::success(['affected' => $count], __('crm.bulk_updated', ['count' => $count]));
    }
}
