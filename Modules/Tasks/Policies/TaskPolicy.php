<?php

namespace Modules\Tasks\Policies;

use Modules\CrmAuth\Models\User;
use Modules\Tasks\Models\Task;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        return $task->tenant_id === $user->tenant_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        if ($task->tenant_id !== $user->tenant_id) return false;
        if ($user->role === 'member') return $task->assigned_to === $user->id;
        return true;
    }

    public function delete(User $user, Task $task): bool
    {
        if ($task->tenant_id !== $user->tenant_id) return false;
        return in_array($user->role, ['owner', 'admin']);
    }
}
