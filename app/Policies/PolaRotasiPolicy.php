<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PolaRotasi;

class PolaRotasiPolicy
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
        return $user->isAdminHr() || $user->isDirektur() || $user->isHod() || $user->isSupervisor();
    }

    public function view(User $user, PolaRotasi $pola): bool
    {
        return $user->isAdminHr() || $user->isDirektur() || $user->isHod() || $user->isSupervisor();
    }

    public function update(User $user, PolaRotasi $pola): bool
    {
        return false;
    }

    public function generate(User $user, ?int $idDepartemen = null): bool
    {
        if ($user->isSupervisor() || $user->isHod()) {
            if ($idDepartemen === null) {
                return true;
            }
            return $user->karyawan?->id_departemen === $idDepartemen;
        }

        return false;
    }
}
