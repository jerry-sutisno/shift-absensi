<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalShift;
use App\Models\Absensi;
use App\Models\Shift;
use App\Services\AttendanceService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class DashboardPresensiCard extends Component
{
    public ?string $pesanSukses = null;
    public ?string $pesanError = null;

    public function absenMasuk(AttendanceService $service): void
    {
        $this->pesanSukses = null;
        $this->pesanError = null;

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            $this->pesanError = 'Akun Anda tidak terhubung dengan data karyawan.';
            return;
        }

        $now = now('Asia/Jakarta');
        $today = $now->toDateString();

        try {
            $jadwal = JadwalShift::with('shift')
                ->where('id_karyawan', $karyawan->id_karyawan)
                ->where('tanggal', $today)
                ->first();

            if (!$jadwal) {
                // Buat jadwal default GS jika belum ada jadwal terdaftar
                $shiftDefault = Shift::where('kode_shift', 'GS')->first() ?? Shift::first();
                $jadwal = JadwalShift::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'tanggal' => $today,
                    'kode_shift' => $shiftDefault?->kode_shift ?? 'GS',
                    'sumber' => 'MANUAL',
                    'status' => 'PUBLISHED',
                ]);
            }

            if ($jadwal->kode_shift === 'OFF') {
                throw new Exception('Hari ini adalah jadwal Libur Rutin (OFF) Anda. Tidak perlu melakukan absensi.');
            }

            $absensi = $service->catatAbsenMasuk($jadwal, $now, 'MANUAL');

            $statusText = $absensi->status === 'TERLAMBAT'
                ? "TERLAMBAT ({$absensi->menit_terlambat} Menit)"
                : 'HADIR (Tepat Waktu)';

            $this->pesanSukses = "Berhasil Absen Masuk pada {$now->format('H:i:s')} WIB [Status: {$statusText}].";

            // Notifikasi supervisor jika ada departemen
            if ($karyawan->id_departemen) {
                NotificationService::kirimKeDepartemen(
                    $karyawan->id_departemen,
                    'SUPERVISOR',
                    "Presensi Masuk: {$karyawan->nama_karyawan}",
                    "{$karyawan->nama_karyawan} telah absen masuk via dashboard pada {$now->format('H:i:s')} WIB ({$statusText}).",
                    '/absensi/koreksi'
                );
            }
        } catch (\Exception $e) {
            $this->pesanError = $e->getMessage();
        }
    }

    public function absenPulang(AttendanceService $service): void
    {
        $this->pesanSukses = null;
        $this->pesanError = null;

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            $this->pesanError = 'Akun Anda tidak terhubung dengan data karyawan.';
            return;
        }

        $now = now('Asia/Jakarta');

        try {
            $absensi = Absensi::with(['jadwal.shift'])
                ->where('id_karyawan', $karyawan->id_karyawan)
                ->whereNotNull('waktu_masuk')
                ->whereNull('waktu_pulang')
                ->latest('tanggal_shift')
                ->first();

            if (!$absensi) {
                // Cek apakah hari ini sudah absen pulang
                $alreadyHome = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                    ->where('tanggal_shift', $now->toDateString())
                    ->whereNotNull('waktu_pulang')
                    ->first();

                if ($alreadyHome) {
                    throw new Exception("Anda sudah menyelesaikan Absen Pulang hari ini pada {$alreadyHome->waktu_pulang->format('H:i:s')} WIB.");
                }

                throw new Exception('Anda belum melakukan Absen Masuk untuk shift kerja hari ini.');
            }

            $service->catatAbsenPulang($absensi, $now, 'MANUAL');

            $jamKerja = $absensi->menit_kerja ? floor($absensi->menit_kerja / 60) . ' jam ' . ($absensi->menit_kerja % 60) . ' menit' : '-';
            $this->pesanSukses = "Berhasil Absen Pulang pada {$now->format('H:i:s')} WIB. Total jam kerja: {$jamKerja}.";
        } catch (\Exception $e) {
            $this->pesanError = $e->getMessage();
        }
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $karyawan = $user->karyawan;
        $now = now('Asia/Jakarta');
        $today = $now->toDateString();

        $jadwalHariIni = null;
        $absensiHariIni = null;

        if ($karyawan) {
            $jadwalHariIni = JadwalShift::with('shift')
                ->where('id_karyawan', $karyawan->id_karyawan)
                ->where('tanggal', $today)
                ->first();

            $absensiHariIni = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                ->where('tanggal_shift', $today)
                ->first();
        }

        return view('livewire.dashboard-presensi-card', [
            'karyawan' => $karyawan,
            'user' => $user,
            'now' => $now,
            'jadwalHariIni' => $jadwalHariIni,
            'absensiHariIni' => $absensiHariIni,
        ]);
    }
}
