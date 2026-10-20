<?php

namespace App\Policies;

use App\Models\User;
use App\Models\JadwalShift;

class JadwalShiftPolicy
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

    public function view(User $user, JadwalShift $jadwal): bool
    {
        if ($user->isDirektur()) {
            return true;
        }

        $userDept = $user->karyawan?->id_departemen;
        $jadwalDept = $jadwal->karyawan?->id_departemen;

        if ($user->isHod() || $user->isSupervisor()) {
            return $userDept !== null && $userDept === $jadwalDept;
        }

        // Operator: hanya jadwal milik sendiri dan HANYA yang berstatus PUBLISHED
        if ($user->isOperator()) {
            return $user->id_karyawan === $jadwal->id_karyawan && $jadwal->isPublished();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isSupervisor();
    }

    public function update(User $user, JadwalShift $jadwal): bool
    {
        if ($user->isSupervisor()) {
            $userDept = $user->karyawan?->id_departemen;
            $jadwalDept = $jadwal->karyawan?->id_departemen;
            return $userDept !== null && $userDept === $jadwalDept;
        }

        return false;
    }

    public function publish(User $user, ?int $idDepartemen = null): bool
    {
        if ($user->isSupervisor()) {
            if ($idDepartemen === null) {
                return true;
            }
            return $user->karyawan?->id_departemen === $idDepartemen;
        }

        return false;
    }

    public function delete(User $user, JadwalShift $jadwal): bool
    {
        return false;
    }
}
