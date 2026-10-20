<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\JadwalShift;
use App\Models\User;
use App\Models\Shift;
use App\Models\Pengajuan;
use App\Models\Pengaturan;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AttendanceService
{
    /**
     * Catat absensi masuk (BR-03, BR-04, BR-05, BR-08, BR-15).
     *
     * @param JadwalShift $jadwal
     * @param Carbon $waktuServer
     * @param string $metode
     * @return Absensi
     */
    public function catatAbsenMasuk(JadwalShift $jadwal, Carbon $waktuServer, string $metode = 'KIOSK'): Absensi
    {
        $shift = $jadwal->shift;

        // BR-08: Shift OFF tidak butuh absensi (Skenario 5)
        if (!$shift || $shift->is_libur || $jadwal->kode_shift === 'OFF') {
            throw new InvalidArgumentException('Shift OFF tidak memerlukan absensi.');
        }

        // BR-03: tanggal_shift adalah tanggal mulai shift
        $tanggalShift = $jadwal->tanggal->toDateString();
        $jamMasukShift = Carbon::parse($tanggalShift . ' ' . $shift->jam_masuk);
        $jamPulangShift = Carbon::parse($tanggalShift . ' ' . $shift->jam_pulang);

        if ($shift->lintas_hari) {
            $jamPulangShift->addDay();
        }

        // BR-04: Jendela absen masuk dibuka N menit sebelum jam_masuk (default 60 menit)
        $jendelaMasukMenit = (int) (Pengaturan::where('kunci', 'jendela_absen_masuk_menit')->value('nilai') ?? 60);
        $bukaAbsen = $jamMasukShift->copy()->subMinutes($jendelaMasukMenit);

        if ($waktuServer->lessThan($bukaAbsen)) {
            throw new InvalidArgumentException("Absen masuk belum dibuka. Jendela absen dibuka pada {$bukaAbsen->format('H:i')} WIB.");
        }

        if ($waktuServer->greaterThan($jamPulangShift)) {
            throw new InvalidArgumentException("Batas waktu shift telah berakhir pada {$jamPulangShift->format('H:i')} WIB.");
        }

        // Cek toleransi keterlambatan (BR-05)
        $toleransiMenit = (int) (Pengaturan::where('kunci', 'toleransi_terlambat_menit')->value('nilai') ?? 10);
        $batasTepatWaktu = $jamMasukShift->copy()->addMinutes($toleransiMenit);

        $status = 'HADIR';
        $menitTerlambat = 0;

        if ($waktuServer->greaterThan($batasTepatWaktu)) {
            $status = 'TERLAMBAT';
            // Menit terlambat dihitung dari jam_masuk shift (BR-05)
            $menitTerlambat = (int) $jamMasukShift->diffInMinutes($waktuServer);
        }

        return Absensi::updateOrCreate(
            [
                'id_karyawan' => $jadwal->id_karyawan,
                'tanggal_shift' => $tanggalShift,
            ],
            [
                'id_jadwal' => $jadwal->id_jadwal,
                'waktu_masuk' => $waktuServer,
                'metode_masuk' => $metode,
                'status' => $status,
                'menit_terlambat' => $menitTerlambat,
            ]
        );
    }

    /**
     * Catat absensi pulang (BR-06).
     */
    public function catatAbsenPulang(Absensi $absensi, Carbon $waktuServer, string $metode = 'KIOSK'): Absensi
    {
        $jadwal = $absensi->jadwal;
        $shift = $jadwal?->shift;

        $tanggalShift = $absensi->tanggal_shift->toDateString();
        $jamPulangShift = Carbon::parse($tanggalShift . ' ' . $shift->jam_pulang);

        if ($shift && $shift->lintas_hari) {
            $jamPulangShift->addDay();
        }

        $pulangCepat = false;
        if ($waktuServer->lessThan($jamPulangShift)) {
            $pulangCepat = true;
        }

        $menitKerja = null;
        if ($absensi->waktu_masuk) {
            $menitKerja = (int) $absensi->waktu_masuk->diffInMinutes($waktuServer);
        }

        $absensi->update([
            'waktu_pulang' => $waktuServer,
            'metode_pulang' => $metode,
            'pulang_cepat' => $pulangCepat,
            'menit_kerja' => $menitKerja,
        ]);

        return $absensi;
    }

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
     * Job ALPA otomatis: Memproses jadwal tanggal terkait (BR-07, Skenario 6).
     * Jika tidak ada absen masuk sampai batas waktu:
     * - Jika ada pengajuan CUTI/IZIN/SAKIT yang DISETUJUI, status menjadi CUTI/IZIN/SAKIT.
     * - Jika tidak ada, status menjadi ALPA.
     */
    public function prosesAlpaOtomatis(?string $tanggal = null): int
    {
        $targetDate = $tanggal ?: now('Asia/Jakarta')->toDateString();

        $jadwals = JadwalShift::with(['shift', 'karyawan'])
            ->where('tanggal', $targetDate)
            ->where('kode_shift', '!=', 'OFF')
            ->where('status', 'PUBLISHED')
            ->get();

        $processed = 0;

        foreach ($jadwals as $j) {
            // Cek apakah sudah ada record absensi dengan waktu masuk
            $absensi = Absensi::where('id_jadwal', $j->id_jadwal)->first();
            if ($absensi && $absensi->waktu_masuk) {
                continue; // Sudah absen masuk
            }

            // Cek pengajuan cuti/izin/sakit yang DISETUJUI pada tanggal ini
            $pengajuan = Pengajuan::where('id_karyawan', $j->id_karyawan)
                ->where('status', 'DISETUJUI')
                ->where('tgl_mulai', '<=', $targetDate)
                ->where('tgl_selesai', '>=', $targetDate)
                ->first();

            $statusAkhir = 'ALPA';
            if ($pengajuan) {
                $statusAkhir = $pengajuan->jenis; // CUTI, IZIN, atau SAKIT
            }

            Absensi::updateOrCreate(
                [
                    'id_karyawan' => $j->id_karyawan,
                    'tanggal_shift' => $targetDate,
                ],
                [
                    'id_jadwal' => $j->id_jadwal,
                    'status' => $statusAkhir,
                    'catatan' => $pengajuan ? "Otomatis dari pengajuan {$pengajuan->jenis} disetujui" : 'Otomatis oleh sistem (ALPA)',
                ]
            );

            $processed++;
        }

        return $processed;
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
