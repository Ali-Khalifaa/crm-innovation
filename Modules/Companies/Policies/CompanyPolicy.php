<?php

namespace Modules\Companies\Policies;

use Modules\CrmAuth\Models\User;
use Modules\Companies\Models\Company;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Company $company): bool
    {
        return $company->tenant_id === $user->tenant_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['owner', 'admin', 'member']);
    }

    public function update(User $user, Company $company): bool
    {
        if ($company->tenant_id !== $user->tenant_id) return false;
        if ($user->role === 'member') return $company->created_by === $user->id;
        return true;
    }

    public function delete(User $user, Company $company): bool
    {
        return $company->tenant_id === $user->tenant_id
            && in_array($user->role, ['owner', 'admin']);
    }
}
