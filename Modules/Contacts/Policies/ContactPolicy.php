<?php

namespace Modules\Contacts\Policies;

use Modules\Contacts\Models\Contact;
use Modules\CrmAuth\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Contact $contact): bool
    {
        return $contact->tenant_id === $user->tenant_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Contact $contact): bool
    {
        if ($contact->tenant_id !== $user->tenant_id) return false;
        // Members can only edit contacts they created
        if ($user->role === 'member') return $contact->created_by === $user->id;
        return true;
    }

    public function delete(User $user, Contact $contact): bool
    {
        if ($contact->tenant_id !== $user->tenant_id) return false;
        return in_array($user->role, ['owner', 'admin']);
    }
}
