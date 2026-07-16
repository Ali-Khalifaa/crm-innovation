<?php

namespace Modules\Deals\Policies;

use Modules\CrmAuth\Models\User;
use Modules\Deals\Models\Deal;

class DealPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Deal $deal): bool
    {
        return $deal->tenant_id === $user->tenant_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Deal $deal): bool
    {
        if ($deal->tenant_id !== $user->tenant_id) return false;
        if ($user->role === 'member') return $deal->assigned_to === $user->id;
        return true;
    }

    public function delete(User $user, Deal $deal): bool
    {
        if ($deal->tenant_id !== $user->tenant_id) return false;
        return in_array($user->role, ['owner', 'admin']);
    }
}
