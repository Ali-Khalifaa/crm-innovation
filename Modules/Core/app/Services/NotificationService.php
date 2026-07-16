<?php

namespace Modules\Core\Services;

use Modules\Core\Models\CrmNotification;

class NotificationService
{
    /**
     * Create a CRM in-app notification.
     *
     * @param int    $tenantId
     * @param int    $userId   — the recipient
     * @param string $type     — task_assigned | deal_assigned | deal_won | deal_lost | contact_created | invoice_overdue
     * @param string $title
     * @param string|null $body
     * @param array  $data     — e.g. ['url' => '/crm/deals/5', 'id' => 5]
     */
    public static function notify(
        int $tenantId,
        int $userId,
        string $type,
        string $title,
        ?string $body = null,
        array $data = []
    ): void {
        CrmNotification::create([
            'tenant_id' => $tenantId,
            'user_id'   => $userId,
            'type'      => $type,
            'title'     => $title,
            'body'      => $body,
            'data'      => $data ?: null,
        ]);
    }

    /**
     * Notify all users in a tenant except the actor.
     */
    public static function notifyTenant(
        int $tenantId,
        int $actorId,
        string $type,
        string $title,
        ?string $body = null,
        array $data = []
    ): void {
        $users = \Modules\CrmAuth\Models\User::where('tenant_id', $tenantId)
            ->where('id', '!=', $actorId)
            ->where('status', 'active')
            ->pluck('id');

        foreach ($users as $userId) {
            static::notify($tenantId, $userId, $type, $title, $body, $data);
        }
    }
}
