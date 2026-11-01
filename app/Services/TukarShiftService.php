<?php

namespace App\Services;

use App\Models\TukarShift;
use App\Models\JadwalShift;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Shift;
use App\Models\Pengaturan;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TukarShiftService
{
    /**
     * Ajukan pertukaran shift antar operator.
     * Status awal: MENUNGGU_PENGGANTI.
     */
    public function ajukanTukar(
        Karyawan $pemohon,
        string $idPengganti,
        int $idJadwalPemohon,
        int $idJadwalPengganti,
        string $alasan
    ): TukarShift {
        $pengganti = Karyawan::findOrFail($idPengganti);

        // BR-10: Kedua karyawan wajib satu departemen
        if ($pemohon->id_departemen !== $pengganti->id_departemen) {
            throw new InvalidArgumentException('Pertukaran shift hanya dapat dilakukan antar karyawan dalam satu departemen.');
        }

        if ($pemohon->id_karyawan === $pengganti->id_karyawan) {
            throw new InvalidArgumentException('Tidak dapat mengajukan tukar shift dengan diri sendiri.');
        }

        $jadwalPemohon = JadwalShift::with('shift')->findOrFail($idJadwalPemohon);
        $jadwalPengganti = JadwalShift::with('shift')->findOrFail($idJadwalPengganti);

        if ($jadwalPemohon->id_karyawan !== $pemohon->id_karyawan) {
            throw new InvalidArgumentException('Jadwal pemohon tidak sesuai dengan karyawan pemohon.');
        }

        if ($jadwalPengganti->id_karyawan !== $pengganti->id_karyawan) {
            throw new InvalidArgumentException('Jadwal pengganti tidak sesuai dengan karyawan pengganti.');
        }

        // Validasi istirahat minimal (BR-10)
        $this->validasiIstirahatMinimal($pemohon, $jadwalPemohon, $jadwalPengganti);
        $this->validasiIstirahatMinimal($pengganti, $jadwalPengganti, $jadwalPemohon);

        $tukar = TukarShift::create([
            'id_pemohon' => $pemohon->id_karyawan,
            'id_pengganti' => $pengganti->id_karyawan,
            'id_jadwal_pemohon' => $jadwalPemohon->id_jadwal,
            'id_jadwal_pengganti' => $jadwalPengganti->id_jadwal,
            'alasan' => $alasan,
            'status' => 'MENUNGGU_PENGGANTI',
        ]);

        // Kirim notifikasi ke operator pengganti
        NotificationService::kirimKeKaryawan(
            $pengganti->id_karyawan,
            'Permintaan Tukar Shift',
            "{$pemohon->nama_karyawan} mengajukan tukar shift untuk tanggal {$jadwalPemohon->tanggal->format('d-m-Y')}. Silakan tinjau dan tanggapi.",
            '/pengajuan-approval'
        );

        return $tukar;
    }

    /**
     * Operator pengganti menerima atau menolak permohonan.
     */
    public function responPengganti(TukarShift $tukar, User $userPengganti, bool $setuju): TukarShift
    {
        if ($tukar->id_pengganti !== $userPengganti->id_karyawan) {
            throw new InvalidArgumentException('Anda bukan operator pengganti untuk permohonan tukar shift ini.');
        }

        if ($tukar->status !== 'MENUNGGU_PENGGANTI') {
            throw new InvalidArgumentException('Status permohonan tukar shift sudah tidak dalam status menunggu pengganti.');
        }

        if ($setuju) {
            $tukar->update(['status' => 'MENUNGGU_SUPERVISOR']);

            // Beritahu Supervisor departemen pemohon
            NotificationService::kirimKeDepartemen(
                $tukar->pemohon->id_departemen,
                'SUPERVISOR',
                'Persetujuan Tukar Shift Operator',
                "Tukar shift antara {$tukar->pemohon->nama_karyawan} dan {$tukar->pengganti->nama_karyawan} telah disepakati kedua operator dan menunggu persetujuan Anda.",
                '/pengajuan-approval'
            );
        } else {
            $tukar->update([
                'status' => 'DITOLAK',
                'keputusan_at' => now('Asia/Jakarta'),
            ]);

            NotificationService::kirimKeKaryawan(
                $tukar->id_pemohon,
                'Tukar Shift Ditolak',
                "{$tukar->pengganti->nama_karyawan} menolak permohonan tukar shift Anda.",
                '/pengajuan-approval'
            );
        }

        return $tukar;
    }

    /**
     * Supervisor menyetujui atau menolak permohonan tukar shift.
     * Jika disetujui, jadwal kedua karyawan otomatis ditukar (BR-10).
     */
    public function approveSupervisor(TukarShift $tukar, User $supervisor, bool $setuju): TukarShift
    {
        return DB::transaction(function () use ($tukar, $supervisor, $setuju) {
            if ($tukar->status !== 'MENUNGGU_SUPERVISOR') {
                throw new InvalidArgumentException('Permohonan belum disepakati kedua operator atau sudah diputuskan.');
            }

            // Validasi: Supervisor tidak boleh menyetujui tukar shift yang melibatkan dirinya sendiri (Rule 12)
            if ($supervisor->id_karyawan === $tukar->id_pemohon || $supervisor->id_karyawan === $tukar->id_pengganti) {
                throw new InvalidArgumentException('Supervisor tidak dapat menyetujui tukar shift yang melibatkan dirinya sendiri.');
            }

            // Validasi supervisor departemen
            if (!$supervisor->isAdminHr()) {
                if ($supervisor->karyawan?->id_departemen !== $tukar->pemohon->id_departemen) {
                    throw new InvalidArgumentException('Supervisor hanya dapat menyetujui tukar shift di departemennya sendiri.');
                }
            }

            $dataLama = $tukar->toArray();

            if ($setuju) {
                $jadwalPemohon = JadwalShift::findOrFail($tukar->id_jadwal_pemohon);
                $jadwalPengganti = JadwalShift::findOrFail($tukar->id_jadwal_pengganti);

                $shiftPemohonLama = $jadwalPemohon->kode_shift;
                $shiftPenggantiLama = $jadwalPengganti->kode_shift;

                // Swap shift codes
                $jadwalPemohon->update([
                    'kode_shift' => $shiftPenggantiLama,
                    'sumber' => 'TUKAR_SHIFT',
                ]);

                $jadwalPengganti->update([
                    'kode_shift' => $shiftPemohonLama,
                    'sumber' => 'TUKAR_SHIFT',
                ]);

                $tukar->update([
                    'status' => 'DISETUJUI',
                    'id_approver' => $supervisor->id_user,
                    'keputusan_at' => now('Asia/Jakarta'),
                ]);

                AuditLog::catat(
                    'APPROVE_TUKAR_SHIFT',
                    'tukar_shift',
                    (string) $tukar->id_tukar,
                    $dataLama,
                    [
                        'status' => 'DISETUJUI',
                        'approver' => $supervisor->id_karyawan,
                        'jadwal_pemohon_baru' => $shiftPenggantiLama,
                        'jadwal_pengganti_baru' => $shiftPemohonLama,
                    ]
                );

                // Notifikasi ke kedua pihak
                NotificationService::kirimKeKaryawan(
                    $tukar->id_pemohon,
                    'Tukar Shift Disetujui',
                    "Permohonan tukar shift Anda dengan {$tukar->pengganti->nama_karyawan} telah disetujui Supervisor. Jadwal telah disesuaikan.",
                    '/pengajuan-approval'
                );

                NotificationService::kirimKeKaryawan(
                    $tukar->id_pengganti,
                    'Tukar Shift Disetujui',
                    "Permohonan tukar shift Anda dengan {$tukar->pemohon->nama_karyawan} telah disetujui Supervisor. Jadwal telah disesuaikan.",
                    '/pengajuan-approval'
                );
            } else {
                $tukar->update([
                    'status' => 'DITOLAK',
                    'id_approver' => $supervisor->id_user,
                    'keputusan_at' => now('Asia/Jakarta'),
                ]);

                AuditLog::catat(
                    'REJECT_TUKAR_SHIFT',
                    'tukar_shift',
                    (string) $tukar->id_tukar,
                    $dataLama,
                    [
                        'status' => 'DITOLAK',
                        'approver' => $supervisor->id_karyawan,
                    ]
                );

                NotificationService::kirimKeKaryawan(
                    $tukar->id_pemohon,
                    'Tukar Shift Ditolak',
                    "Permohonan tukar shift Anda ditolak oleh Supervisor ({$supervisor->nama}).",
                    '/pengajuan-approval'
                );
            }

            return $tukar;
        });
    }

    /**
     * Validasi istirahat minimal antar shift (BR-10).
     */
    protected function validasiIstirahatMinimal(Karyawan $karyawan, JadwalShift $jadwalLama, JadwalShift $jadwalTujuan): void
    {
        $minIstirahat = (int) (Pengaturan::where('kunci', 'min_istirahat_jam')->value('nilai') ?? 8);
        $shiftBaru = Shift::find($jadwalTujuan->kode_shift);

        if (!$shiftBaru || $shiftBaru->is_libur) {
            return; // Libur / OFF selalu aman
        }

        // Cek jadwal hari sebelumnya
        $tglSebelumnya = $jadwalLama->tanggal->copy()->subDay()->toDateString();
        $jadwalSebelum = JadwalShift::with('shift')
            ->where('id_karyawan', $karyawan->id_karyawan)
            ->where('tanggal', $tglSebelumnya)
            ->first();

        if ($jadwalSebelum && $jadwalSebelum->shift && !$jadwalSebelum->shift->is_libur) {
            $jamPulangSebelum = Carbon::parse($tglSebelumnya . ' ' . $jadwalSebelum->shift->jam_pulang);
            if ($jadwalSebelum->shift->lintas_hari) {
                $jamPulangSebelum->addDay();
            }

            $jamMasukBaru = Carbon::parse($jadwalLama->tanggal->toDateString() . ' ' . $shiftBaru->jam_masuk);

            $istirahatJam = $jamPulangSebelum->diffInHours($jamMasukBaru, false);
            if ($istirahatJam >= 0 && $istirahatJam < $minIstirahat) {
                throw new InvalidArgumentException(
                    "Jeda istirahat untuk {$karyawan->nama_karyawan} hanya {$istirahatJam} jam (minimal {$minIstirahat} jam sesuai regulasi Depnaker)."
                );
            }
        }

        // Cek jadwal hari berikutnya
        $tglBerikutnya = $jadwalLama->tanggal->copy()->addDay()->toDateString();
        $jadwalBerikut = JadwalShift::with('shift')
            ->where('id_karyawan', $karyawan->id_karyawan)
            ->where('tanggal', $tglBerikutnya)
            ->first();

        if ($jadwalBerikut && $jadwalBerikut->shift && !$jadwalBerikut->shift->is_libur) {
            $jamPulangBaru = Carbon::parse($jadwalLama->tanggal->toDateString() . ' ' . $shiftBaru->jam_pulang);
            if ($shiftBaru->lintas_hari) {
                $jamPulangBaru->addDay();
            }

            $jamMasukBerikut = Carbon::parse($tglBerikutnya . ' ' . $jadwalBerikut->shift->jam_masuk);

            $istirahatJamBerikut = $jamPulangBaru->diffInHours($jamMasukBerikut, false);
            if ($istirahatJamBerikut >= 0 && $istirahatJamBerikut < $minIstirahat) {
                throw new InvalidArgumentException(
                    "Jeda istirahat untuk {$karyawan->nama_karyawan} ke shift berikutnya hanya {$istirahatJamBerikut} jam (minimal {$minIstirahat} jam sesuai regulasi Depnaker)."
                );
            }
        }
    }
}
