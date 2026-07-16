<?php

namespace Modules\Invoices\Policies;

use Modules\CrmAuth\Models\User;
use Modules\Invoices\Models\Invoice;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $invoice->tenant_id === $user->tenant_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['owner', 'admin']);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if ($invoice->tenant_id !== $user->tenant_id) return false;
        return in_array($user->role, ['owner', 'admin']);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        if ($invoice->tenant_id !== $user->tenant_id) return false;
        return $user->role === 'owner';
    }

    public function updateStatus(User $user, Invoice $invoice): bool
    {
        if ($invoice->tenant_id !== $user->tenant_id) return false;
        return in_array($user->role, ['owner', 'admin']);
    }
}
