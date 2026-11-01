<?php

namespace App\Services;

use App\Models\Pengajuan;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Pengaturan;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PengajuanService
{
    /**
     * Ajukan izin, sakit, atau cuti.
     * Alur awal: Masuk ke tahap SUPERVISOR.
     */
    public function ajukan(
        Karyawan $pemohon,
        string $jenis,
        string $tglMulai,
        string $tglSelesai,
        string $alasan,
        ?string $lampiranPath = null
    ): Pengajuan {
        if (!in_array($jenis, ['CUTI', 'IZIN', 'SAKIT'])) {
            throw new InvalidArgumentException("Jenis pengajuan '{$jenis}' tidak valid.");
        }

        $mulai = Carbon::parse($tglMulai);
        $selesai = Carbon::parse($tglSelesai);

        if ($selesai->lt($mulai)) {
            throw new InvalidArgumentException('Tanggal selesai tidak boleh sebelum tanggal mulai.');
        }

        $pengajuan = Pengajuan::create([
            'id_karyawan' => $pemohon->id_karyawan,
            'jenis' => $jenis,
            'tgl_mulai' => $mulai->toDateString(),
            'tgl_selesai' => $selesai->toDateString(),
            'alasan' => $alasan,
            'lampiran_path' => $lampiranPath,
            'status' => 'DIAJUKAN',
            'tahap' => 'SUPERVISOR',
        ]);

        AuditLog::catat(
            'AJUKAN_PENGAJUAN',
            'pengajuan',
            (string) $pengajuan->id_pengajuan,
            null,
            $pengajuan->toArray()
        );

        // Notifikasi ke Supervisor departemen pemohon
        if ($pemohon->id_departemen) {
            NotificationService::kirimKeDepartemen(
                $pemohon->id_departemen,
                'SUPERVISOR',
                "Permohonan {$jenis} Baru",
                "{$pemohon->nama_karyawan} mengajukan {$jenis} ({$mulai->format('d/m/Y')} s/d {$selesai->format('d/m/Y')}).",
                '/pengajuan-approval'
            );
        }

        return $pengajuan;
    }

    /**
     * Setujui atau tolak pengajuan cuti/izin/sakit.
     * Rule 12: Operator -> Supervisor -> HOD (jika cuti melebihi batas hari).
     */
    public function approve(
        Pengajuan $pengajuan,
        User $approver,
        bool $setuju,
        ?string $catatan = null
    ): Pengajuan {
        return DB::transaction(function () use ($pengajuan, $approver, $setuju, $catatan) {
            $pemohon = $pengajuan->karyawan;
            if (!$pemohon) {
                throw new InvalidArgumentException('Data karyawan pemohon tidak ditemukan.');
            }

            // Validasi otorisasi departemen approver
            if (!$approver->isAdminHr()) {
                if ($approver->id_karyawan === $pemohon->id_karyawan) {
                    throw new InvalidArgumentException('Approver tidak dapat memproses permohonan milik sendiri.');
                }

                $approverDept = $approver->karyawan?->id_departemen;
                if (!$approverDept || $approverDept !== $pemohon->id_departemen) {
                    throw new InvalidArgumentException('Hanya approver dalam satu departemen yang berhak memproses permohonan.');
                }
            }

            $dataLama = $pengajuan->toArray();

            if (!$setuju) {
                $pengajuan->update([
                    'status' => 'DITOLAK',
                    'tahap' => 'SELESAI',
                    'id_approver' => $approver->id_user,
                    'catatan_approver' => $catatan ?? 'Permohonan ditolak oleh atasan.',
                    'keputusan_at' => now('Asia/Jakarta'),
                ]);

                AuditLog::catat(
                    'REJECT_PENGAJUAN',
                    'pengajuan',
                    (string) $pengajuan->id_pengajuan,
                    $dataLama,
                    $pengajuan->fresh()->toArray()
                );

                NotificationService::kirimKeKaryawan(
                    $pemohon->id_karyawan,
                    "Permohonan {$pengajuan->jenis} Ditolak",
                    "Permohonan {$pengajuan->jenis} Anda telah ditolak. Catatan: " . ($catatan ?? 'Tidak ada catatan.'),
                    '/pengajuan-approval'
                );

                return $pengajuan;
            }

            // Skenario Persetujuan (SETUJU)
            $mulai = Carbon::parse($pengajuan->tgl_mulai);
            $selesai = Carbon::parse($pengajuan->tgl_selesai);
            $durasiHari = $mulai->diffInDays($selesai) + 1;
            $batasCutiHod = (int) Pengaturan::getValue('cuti_perlu_hod_hari', 3);

            // Rule 12: Jika jenis CUTI melebihi batas cuti HOD dan saat ini masih tahap SUPERVISOR
            if ($pengajuan->jenis === 'CUTI' && $durasiHari > $batasCutiHod && $pengajuan->tahap === 'SUPERVISOR') {
                $catatanSup = "Disetujui Supervisor ({$approver->nama}). Diteruskan ke HOD (durasi {$durasiHari} hari > {$batasCutiHod} hari).";
                if ($catatan) {
                    $catatanSup .= " Catatan: {$catatan}";
                }

                $pengajuan->update([
                    'tahap' => 'HOD',
                    'catatan_approver' => $catatanSup,
                ]);

                AuditLog::catat(
                    'ESCALATE_PENGAJUAN_HOD',
                    'pengajuan',
                    (string) $pengajuan->id_pengajuan,
                    $dataLama,
                    $pengajuan->fresh()->toArray()
                );

                // Notifikasi ke HOD departemen
                if ($pemohon->id_departemen) {
                    NotificationService::kirimKeDepartemen(
                        $pemohon->id_departemen,
                        'HOD',
                        'Approval Cuti Memerlukan Persetujuan HOD',
                        "Permohonan Cuti {$pemohon->nama_karyawan} ({$durasiHari} hari) telah disetujui Supervisor dan membutuhkan persetujuan final HOD.",
                        '/pengajuan-approval'
                    );
                }

                return $pengajuan;
            }

            // Persetujuan Final (Tahap HOD, atau Supervisor untuk cuti <= batas atau izin/sakit)
            $pengajuan->update([
                'status' => 'DISETUJUI',
                'tahap' => 'SELESAI',
                'id_approver' => $approver->id_user,
                'catatan_approver' => $catatan,
                'keputusan_at' => now('Asia/Jakarta'),
            ]);

            AuditLog::catat(
                'APPROVE_PENGAJUAN',
                'pengajuan',
                (string) $pengajuan->id_pengajuan,
                $dataLama,
                $pengajuan->fresh()->toArray()
            );

            NotificationService::kirimKeKaryawan(
                $pemohon->id_karyawan,
                "Permohonan {$pengajuan->jenis} Disetujui",
                "Permohonan {$pengajuan->jenis} Anda ({$mulai->format('d/m/Y')} s/d {$selesai->format('d/m/Y')}) telah disetujui.",
                '/pengajuan-approval'
            );

            return $pengajuan;
        });
    }
}
