<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pengaturan;

class PengaturanPolicy
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

    public function update(User $user, Pengaturan $pengaturan): bool
    {
        return false;
    }
}
