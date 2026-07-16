<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Models\CrmNotification;
use Modules\Core\Helpers\ApiResponse;

class NotificationController extends Controller
{
    private function user()
    {
        return auth('tenant_api')->user();
    }

    public function index(): JsonResponse
    {
        $user = $this->user();
        $filter = request('filter', 'all'); // all | unread

        $query = CrmNotification::where('tenant_id', $user->tenant_id)
            ->where('user_id', $user->id)
            ->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        $notifications = $query->paginate(20);

        return ApiResponse::paginated($notifications, function ($n) {
            return [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'body'       => $n->body,
                'data'       => $n->data,
                'read_at'    => $n->read_at?->toIso8601String(),
                'is_read'    => $n->isRead(),
                'created_at' => $n->created_at->toIso8601String(),
                'time_ago'   => $n->created_at->diffForHumans(),
            ];
        });
    }

    public function unreadCount(): JsonResponse
    {
        $user = $this->user();
        $count = CrmNotification::where('tenant_id', $user->tenant_id)
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return ApiResponse::success(['count' => $count]);
    }

    public function markRead(CrmNotification $notification): JsonResponse
    {
        $this->authorizeNotification($notification);
        $notification->markRead();
        return ApiResponse::success(null, __('crm.notification_marked_read'));
    }

    public function markAllRead(): JsonResponse
    {
        $user = $this->user();
        CrmNotification::where('tenant_id', $user->tenant_id)
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return ApiResponse::success(null, __('crm.notifications_all_read'));
    }

    public function destroy(CrmNotification $notification): JsonResponse
    {
        $this->authorizeNotification($notification);
        $notification->delete();
        return ApiResponse::success(null, __('crm.deleted'));
    }

    private function authorizeNotification(CrmNotification $notification): void
    {
        $user = $this->user();
        abort_if(
            $notification->tenant_id !== $user->tenant_id || $notification->user_id !== $user->id,
            403
        );
    }
}
