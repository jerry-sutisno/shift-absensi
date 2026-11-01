<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TukarShift;

class TukarShiftPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdminHr()) {
            if (in_array($ability, ['approveSupervisor', 'responPengganti'])) {
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

    public function view(User $user, TukarShift $tukar): bool
    {
        if ($user->isDirektur()) {
            return true;
        }

        $userDept = $user->karyawan?->id_departemen;
        $tukarDept = $tukar->pemohon?->id_departemen;

        if ($user->isHod() || $user->isSupervisor()) {
            return $userDept !== null && $userDept === $tukarDept;
        }

        if ($user->isOperator()) {
            return $user->id_karyawan === $tukar->id_pemohon || $user->id_karyawan === $tukar->id_pengganti;
        }

        return false;
    }

    public function responPengganti(User $user, TukarShift $tukar): bool
    {
        return $user->id_karyawan === $tukar->id_pengganti && $tukar->status === 'MENUNGGU_PENGGANTI';
    }

    public function approveSupervisor(User $user, TukarShift $tukar): bool
    {
        if ($tukar->status !== 'MENUNGGU_SUPERVISOR') {
            return false;
        }

        // Supervisor yang bertukar shift tidak boleh menyetujui permohonan yang melibatkan dirinya (Rule 12)
        if ($user->id_karyawan === $tukar->id_pemohon || $user->id_karyawan === $tukar->id_pengganti) {
            return false;
        }

        if ($user->isSupervisor() || $user->isHod()) {
            return $user->karyawan?->id_departemen === $tukar->pemohon?->id_departemen;
        }

        return false;
    }
}
