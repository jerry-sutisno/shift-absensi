<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Lembur;

class LemburPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdminHr()) {
            if ($ability === 'approve') {
                return false;
            }
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Lembur $lembur): bool
    {
        if ($user->isDirektur()) {
            return true;
        }

        $userDept = $user->karyawan?->id_departemen;
        $lemburDept = $lembur->karyawan?->id_departemen;

        if ($user->isHod() || $user->isSupervisor()) {
            return $userDept !== null && $userDept === $lemburDept;
        }

        if ($user->isOperator()) {
            return $user->id_karyawan === $lembur->id_karyawan;
        }

        return false;
    }

    public function approve(User $user, Lembur $lembur): bool
    {
        if ($lembur->status !== 'DIAJUKAN') {
            return false;
        }

        // Tidak boleh menyetujui pengajuan lembur milik sendiri (Rule 12)
        if ($user->id_karyawan === $lembur->id_karyawan) {
            return false;
        }

        if ($user->isSupervisor() || $user->isHod()) {
            return $user->karyawan?->id_departemen === $lembur->karyawan?->id_departemen;
        }

        return false;
    }
}
