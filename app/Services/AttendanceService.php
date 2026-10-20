<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\JadwalShift;
use App\Models\User;
use App\Models\Shift;
use App\Models\Pengaturan;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AttendanceService
{
    /**
     * Hitung status absensi berdasarkan waktu server dan jadwal shift (BR-04, BR-05, BR-06).
     */
    public function hitungStatusAbsensi(JadwalShift $jadwal, ?Carbon $waktuMasuk, ?Carbon $waktuPulang = null): array
    {
        $shift = $jadwal->shift;
        if (!$shift || $shift->is_libur) {
            return [
                'status' => 'LIBUR',
                'menit_terlambat' => 0,
                'pulang_cepat' => false,
                'menit_kerja' => null,
            ];
        }

        $toleransiMenit = (int) (Pengaturan::where('kunci', 'toleransi_terlambat_menit')->value('nilai') ?? 10);

        $tanggalShift = $jadwal->tanggal->toDateString();
        $jamMasukShift = Carbon::parse($tanggalShift . ' ' . $shift->jam_masuk);
        $jamPulangShift = Carbon::parse($tanggalShift . ' ' . $shift->jam_pulang);

        if ($shift->lintas_hari) {
            $jamPulangShift->addDay();
        }

        $status = 'HADIR';
        $menitTerlambat = 0;
        $pulangCepat = false;
        $menitKerja = null;

        if ($waktuMasuk) {
            // BR-05: Terlambat jika waktu_masuk > jam_masuk + toleransi
            $batasTepatWaktu = $jamMasukShift->copy()->addMinutes($toleransiMenit);
            if ($waktuMasuk->greaterThan($batasTepatWaktu)) {
                $status = 'TERLAMBAT';
                $menitTerlambat = (int) $jamMasukShift->diffInMinutes($waktuMasuk);
            }
        } else {
            $status = 'ALPA';
        }

        if ($waktuMasuk && $waktuPulang) {
            // BR-06: Pulang cepat jika waktu pulang < jam pulang
            if ($waktuPulang->lessThan($jamPulangShift)) {
                $pulangCepat = true;
            }
            $menitKerja = (int) $waktuMasuk->diffInMinutes($waktuPulang);
        }

        return [
            'status' => $status,
            'menit_terlambat' => $menitTerlambat,
            'pulang_cepat' => $pulangCepat,
            'menit_kerja' => $menitKerja,
        ];
    }

    /**
     * Koreksi manual absensi oleh Admin HR atau Supervisor (F-11 & BR-12).
     */
    public function koreksiAbsensi(int $idAbsensi, array $dataKoreksi, User $user, string $alasan): Absensi
    {
        if (trim($alasan) === '') {
            throw new InvalidArgumentException('Alasan koreksi absensi wajib diisi.');
        }

        return DB::transaction(function () use ($idAbsensi, $dataKoreksi, $user, $alasan) {
            $absensi = Absensi::with(['karyawan', 'jadwal.shift'])->findOrFail($idAbsensi);
            $dataLama = $absensi->toArray();

            // Hitung menit_kerja jika waktu_masuk dan waktu_pulang ada
            $waktuMasuk = !empty($dataKoreksi['waktu_masuk']) ? Carbon::parse($dataKoreksi['waktu_masuk']) : null;
            $waktuPulang = !empty($dataKoreksi['waktu_pulang']) ? Carbon::parse($dataKoreksi['waktu_pulang']) : null;

            $menitKerja = null;
            if ($waktuMasuk && $waktuPulang) {
                $menitKerja = (int) $waktuMasuk->diffInMinutes($waktuPulang);
            }

            $updatePayload = [
                'waktu_masuk' => $waktuMasuk,
                'waktu_pulang' => $waktuPulang,
                'metode_masuk' => $dataKoreksi['metode_masuk'] ?? $absensi->metode_masuk ?? 'MANUAL',
                'metode_pulang' => $dataKoreksi['metode_pulang'] ?? $absensi->metode_pulang ?? 'MANUAL',
                'status' => $dataKoreksi['status'] ?? $absensi->status,
                'menit_terlambat' => $dataKoreksi['menit_terlambat'] ?? 0,
                'pulang_cepat' => $dataKoreksi['pulang_cepat'] ?? false,
                'menit_kerja' => $menitKerja,
                'catatan' => $alasan,
                'dikoreksi_oleh' => $user->id_user,
            ];

            $absensi->update($updatePayload);
            $absensi->refresh();

            // Catat audit log (BR-12)
            AuditLog::catat(
                'KOREKSI_ABSENSI',
                'absensi',
                (string) $absensi->id_absensi,
                $dataLama,
                $absensi->toArray()
            );

            // Kirim notifikasi ke karyawan
            NotificationService::kirimKeKaryawan(
                $absensi->id_karyawan,
                'Koreksi Absensi Dilakukan',
                "Catatan absensi Anda pada tanggal {$absensi->tanggal_shift->format('d-m-Y')} telah dikoreksi oleh {$user->nama}. Alasan: {$alasan}",
                '/dashboard'
            );

            return $absensi;
        });
    }
}
