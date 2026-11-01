<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LogbookShift;

class LogbookShiftPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdminHr()) {
            if ($ability === 'create') {
                return false;
            }
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isSupervisor() || $user->isHod() || $user->isAdminHr() || $user->isDirektur();
    }

    public function view(User $user, LogbookShift $logbook): bool
    {
        if ($user->isDirektur()) {
            return true;
        }

        $userDept = $user->karyawan?->id_departemen;
        return $userDept !== null && $userDept === $logbook->id_departemen;
    }

    public function create(User $user): bool
    {
        return $user->isSupervisor();
    }
}
