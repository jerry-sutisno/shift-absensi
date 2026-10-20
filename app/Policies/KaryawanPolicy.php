<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Karyawan;

class KaryawanPolicy
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

    public function view(User $user, Karyawan $karyawan): bool
    {
        if ($user->isDirektur()) {
            return true;
        }

        if ($user->isHod() || $user->isSupervisor()) {
            return $user->karyawan?->id_departemen === $karyawan->id_departemen;
        }

        return $user->id_karyawan === $karyawan->id_karyawan;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Karyawan $karyawan): bool
    {
        return false;
    }

    public function delete(User $user, Karyawan $karyawan): bool
    {
        return false;
    }
}
