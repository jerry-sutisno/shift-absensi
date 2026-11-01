<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pengajuan;

class PengajuanPolicy
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

    public function view(User $user, Pengajuan $pengajuan): bool
    {
        if ($user->isDirektur()) {
            return true;
        }

        $userDept = $user->karyawan?->id_departemen;
        $pengajuanDept = $pengajuan->karyawan?->id_departemen;

        if ($user->isHod() || $user->isSupervisor()) {
            return $userDept !== null && $userDept === $pengajuanDept;
        }

        if ($user->isOperator()) {
            return $user->id_karyawan === $pengajuan->id_karyawan;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function approve(User $user, Pengajuan $pengajuan): bool
    {
        // Approver tidak boleh menyetujui permohonan miliknya sendiri (Rule 12)
        if ($user->id_karyawan === $pengajuan->id_karyawan) {
            return false;
        }

        $userDept = $user->karyawan?->id_departemen;
        $pengajuanDept = $pengajuan->karyawan?->id_departemen;

        // Approver wajib satu departemen
        if ($userDept === null || $userDept !== $pengajuanDept) {
            return false;
        }

        $pemohonJabatan = $pengajuan->karyawan?->jabatan?->level_jabatan;

        // Tahap Supervisor: bisa disetujui Supervisor atau HOD
        if ($pengajuan->tahap === 'SUPERVISOR') {
            if ($user->isSupervisor() && $pemohonJabatan === 'OPERATOR') {
                return true;
            }
            if ($user->isHod()) {
                return true;
            }
        }

        // Tahap HOD: wajib disetujui HOD
        if ($pengajuan->tahap === 'HOD') {
            return $user->isHod();
        }

        return false;
    }
}
