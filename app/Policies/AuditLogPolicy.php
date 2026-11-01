<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AuditLog;

class AuditLogPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdminHr()) {
            return true;
        }
        return false;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return false;
    }
}
