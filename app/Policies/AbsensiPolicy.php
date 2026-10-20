<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Absensi;

class AbsensiPolicy
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
        return true;
    }

    public function view(User $user, Absensi $absensi): bool
    {
        if ($user->isDirektur()) {
            return true;
        }

        $userDept = $user->karyawan?->id_departemen;
        $absensiDept = $absensi->karyawan?->id_departemen;

        if ($user->isHod() || $user->isSupervisor()) {
            return $userDept !== null && $userDept === $absensiDept;
        }

        if ($user->isOperator()) {
            return $user->id_karyawan === $absensi->id_karyawan;
        }

        return false;
    }

    public function koreksi(User $user, Absensi $absensi): bool
    {
        if ($user->isSupervisor()) {
            $userDept = $user->karyawan?->id_departemen;
            $absensiDept = $absensi->karyawan?->id_departemen;
            return $userDept !== null && $userDept === $absensiDept;
        }

        return false;
    }

    public function update(User $user, Absensi $absensi): bool
    {
        return $this->koreksi($user, $absensi);
    }
}
