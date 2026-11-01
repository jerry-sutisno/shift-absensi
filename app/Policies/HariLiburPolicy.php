<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HariLibur;

class HariLiburPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdminHr()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, HariLibur $hariLibur): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, HariLibur $hariLibur): bool
    {
        return false;
    }

    public function delete(User $user, HariLibur $hariLibur): bool
    {
        return false;
    }
}
