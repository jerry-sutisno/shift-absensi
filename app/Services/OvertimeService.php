<?php

namespace App\Services;

use App\Models\Lembur;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OvertimeService
{
    /**
     * Operator atau Supervisor mengajukan lembur (F-12).
     */
    public function ajukanLembur(
        Karyawan $karyawan,
        string $tanggal,
        string $jamMulai,
        string $jamSelesai,
        string $alasan
    ): Lembur {
        if (trim($alasan) === '') {
            throw new InvalidArgumentException('Alasan pengajuan lembur wajib diisi.');
        }

        $mulai = Carbon::parse($tanggal . ' ' . $jamMulai);
        $selesai = Carbon::parse($tanggal . ' ' . $jamSelesai);

        if ($selesai->lessThanOrEqualTo($mulai)) {
            // Lembur lintas hari (misal 22:00 s.d 02:00)
            $selesai->addDay();
        }

        $menitLembur = (int) $mulai->diffInMinutes($selesai);

        $lembur = Lembur::create([
            'id_karyawan' => $karyawan->id_karyawan,
            'tanggal' => $tanggal,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'alasan' => $alasan,
            'status' => 'DIAJUKAN',
            'menit_lembur' => $menitLembur,
        ]);

        // Kirim notifikasi ke Supervisor departemen terkait
        NotificationService::kirimKeDepartemen(
            $karyawan->id_departemen,
            'SUPERVISOR',
            'Pengajuan Lembur Baru',
            "{$karyawan->nama_karyawan} mengajukan lembur pada tanggal {$tanggal} ({$menitLembur} menit). Alasan: {$alasan}",
            '/pengajuan-approval'
        );

        return $lembur;
    }

    /**
     * Supervisor atau Admin HR menyetujui atau menolak lembur.
     */
    public function approveLembur(Lembur $lembur, User $approver, bool $setuju): Lembur
    {
        return DB::transaction(function () use ($lembur, $approver, $setuju) {
            if ($lembur->status !== 'DIAJUKAN') {
                throw new InvalidArgumentException('Pengajuan lembur ini sudah diputuskan.');
            }

            // Validasi: Approver tidak dapat menyetujui pengajuan lembur milik sendiri (Rule 12)
            if ($approver->id_karyawan === $lembur->id_karyawan) {
                throw new InvalidArgumentException('Approver tidak dapat memproses pengajuan lembur milik sendiri.');
            }

            // Approver wajib satu departemen jika supervisor
            if ($approver->isSupervisor()) {
                if ($approver->karyawan?->id_departemen !== $lembur->karyawan?->id_departemen) {
                    throw new InvalidArgumentException('Supervisor hanya dapat menyetujui lembur untuk staf di departemennya.');
                }
            }

            $dataLama = $lembur->toArray();
            $statusBaru = $setuju ? 'DISETUJUI' : 'DITOLAK';

            $lembur->update([
                'status' => $statusBaru,
                'id_approver' => $approver->id_user,
            ]);

            AuditLog::catat(
                $setuju ? 'APPROVE_LEMBUR' : 'REJECT_LEMBUR',
                'lembur',
                (string) $lembur->id_lembur,
                $dataLama,
                $lembur->fresh()->toArray()
            );

            // Notifikasi ke pemohon
            NotificationService::kirimKeKaryawan(
                $lembur->id_karyawan,
                $setuju ? 'Pengajuan Lembur Disetujui' : 'Pengajuan Lembur Ditolak',
                "Pengajuan lembur Anda untuk tanggal {$lembur->tanggal->format('d-m-Y')} telah " . ($setuju ? 'disetujui' : 'ditolak') . " oleh {$approver->nama}.",
                '/pengajuan-approval'
            );

            return $lembur;
        });
    }
}
