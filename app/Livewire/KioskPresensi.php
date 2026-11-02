<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\User;
use App\Models\Shift;
use App\Models\JadwalShift;
use App\Models\Absensi;
use App\Services\AttendanceService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;
use Exception;

#[Layout('layouts.kiosk')]
#[Title('Kiosk Presensi Mandiri Operator')]
class KioskPresensi extends Component
{
    public string $id_karyawan = '';
    public string $pin = '';
    public ?array $konfirmasi = null;
    public ?string $errorMessage = null;
    public ?string $selectedNama = null;
    public ?string $selectedDepartemen = null;
    public ?string $selectedJabatan = null;
    public ?string $selectedRegu = null;
    public ?string $selectedShiftHariIni = null;
    public ?string $statusAbsenHariIni = null;

    public function updatedIdKaryawan($val): void
    {
        $this->errorMessage = null;
        $id = trim($val);
        if (empty($id)) {
            $this->selectedNama = null;
            $this->selectedDepartemen = null;
            $this->selectedJabatan = null;
            $this->selectedRegu = null;
            $this->selectedShiftHariIni = null;
            $this->statusAbsenHariIni = null;
            return;
        }

        $karyawan = Karyawan::with(['departemen', 'jabatan', 'grup'])->find($id);
        if ($karyawan) {
            $this->selectedNama = $karyawan->nama_karyawan;
            $this->selectedDepartemen = $karyawan->departemen?->nama_departemen;
            $this->selectedJabatan = $karyawan->jabatan?->nama_jabatan;
            $this->selectedRegu = $karyawan->grup?->nama_grup ?? 'Grup A';

            $today = now('Asia/Jakarta')->toDateString();
            $jadwal = JadwalShift::with('shift')
                ->where('id_karyawan', $karyawan->id_karyawan)
                ->where('tanggal', $today)
                ->first();

            $this->selectedShiftHariIni = $jadwal?->shift 
                ? "{$jadwal->shift->kode_shift} ({$jadwal->shift->jam_masuk} - {$jadwal->shift->jam_pulang})" 
                : 'S1 (07:00 - 15:00)';

            $absensi = Absensi::where('id_karyawan', $karyawan->id_karyawan)
                ->where('tanggal_shift', $today)
                ->first();

            if ($absensi) {
                if ($absensi->waktu_pulang) {
                    $this->statusAbsenHariIni = 'Sudah Pulang (' . Carbon::parse($absensi->waktu_pulang)->format('H:i') . ')';
                } elseif ($absensi->waktu_masuk) {
                    $this->statusAbsenHariIni = 'Sudah Masuk (' . Carbon::parse($absensi->waktu_masuk)->format('H:i') . ')';
                } else {
                    $this->statusAbsenHariIni = 'Belum Absen';
                }
            } else {
                $this->statusAbsenHariIni = 'Belum Absen';
            }
        } else {
            $this->selectedNama = null;
            $this->selectedDepartemen = null;
            $this->selectedJabatan = null;
            $this->selectedRegu = null;
            $this->selectedShiftHariIni = null;
            $this->statusAbsenHariIni = null;
        }
    }

    public function pressKey(string $digit): void
    {
        $this->errorMessage = null;
        if (strlen($this->pin) < 6) {
            $this->pin .= $digit;
        }
    }

    public function clearPin(): void
    {
        $this->pin = '';
        $this->errorMessage = null;
    }

    public function backspacePin(): void
    {
        $this->errorMessage = null;
        $this->pin = substr($this->pin, 0, -1);
    }

    public function pilihKaryawanDemo(string $id): void
    {
        $this->id_karyawan = $id;
        $this->pin = '123456'; // Default PIN demo
        $this->updatedIdKaryawan($id);
    }

    public function resetKiosk(): void
    {
        $this->id_karyawan = '';
        $this->pin = '';
        $this->konfirmasi = null;
        $this->errorMessage = null;
        $this->selectedNama = null;
        $this->selectedDepartemen = null;
        $this->selectedJabatan = null;
        $this->selectedRegu = null;
        $this->selectedShiftHariIni = null;
        $this->statusAbsenHariIni = null;
    }

    /**
     * Memproses Absensi Masuk (F-05 & FR-12).
     */
    public function prosesAbsenMasuk(AttendanceService $service): void
    {
        $this->prosesAbsensi('MASUK', $service);
    }

    /**
     * Memproses Absensi Pulang (F-05 & FR-12).
     */
    public function prosesAbsenPulang(AttendanceService $service): void
    {
        $this->prosesAbsensi('PULANG', $service);
    }

    protected function prosesAbsensi(string $tipe, AttendanceService $service): void
    {
        $this->errorMessage = null;
        $this->konfirmasi = null;

        $id = trim($this->id_karyawan);
        if (empty($id)) {
            $this->errorMessage = 'Silakan masukkan NIK / ID Karyawan Anda.';
            return;
        }

        if (strlen($this->pin) !== 6) {
            $this->errorMessage = 'PIN Kiosk harus terdiri dari 6 digit angka.';
            return;
        }

        // Rate limiting pada terminal Kiosk (5 percobaan salah per IP & Karyawan)
        $throttleKey = 'kiosk_pin|' . $id . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->errorMessage = "Terlalu banyak percobaan PIN salah. Terminal dikunci sementara selama {$seconds} detik.";
            return;
        }

        $user = User::with('karyawan.departemen', 'karyawan.jabatan')->where('id_karyawan', $id)->first();
        if (!$user || !$user->is_active || !$user->karyawan) {
            RateLimiter::hit($throttleKey, 60);
            $this->errorMessage = 'ID Karyawan tidak terdaftar atau status akun sedang nonaktif.';
            return;
        }

        // Verifikasi PIN 6-digit
        if (!$user->pin_absen || !Hash::check($this->pin, $user->pin_absen)) {
            RateLimiter::hit($throttleKey, 60);
            $this->errorMessage = 'PIN Kiosk tidak sesuai. Silakan hubungi Supervisor / HR jika lupa PIN.';
            return;
        }

        RateLimiter::clear($throttleKey);

        $now = now('Asia/Jakarta');
        $today = $now->toDateString();
        $karyawan = $user->karyawan;

        try {
            if ($tipe === 'MASUK') {
                // Cari jadwal shift hari ini (atau kemarin jika shift malam lintas hari)
                $jadwal = JadwalShift::with('shift')
                    ->where('id_karyawan', $karyawan->id_karyawan)
                    ->where('tanggal', $today)
                    ->first();

                if (!$jadwal) {
                    throw new Exception("Tidak ditemukan jadwal kerja terdaftar untuk tanggal {$now->format('d/m/Y')}.");
                }

                if ($jadwal->kode_shift === 'OFF') {
                    throw new Exception("Hari ini adalah jadwal Libur Rutin (OFF) Anda. Tidak perlu melakukan absensi.");
                }

                $absensi = $service->catatAbsenMasuk($jadwal, $now, 'KIOSK');

                $statusLabel = $absensi->status === 'TERLAMBAT'
                    ? "TERLAMBAT ({$absensi->menit_terlambat} Menit)"
                    : 'HADIR (Tepat Waktu)';

                $statusColor = $absensi->status === 'TERLAMBAT' ? 'amber' : 'emerald';

                $this->konfirmasi = [
                    'tipe' => 'MASUK',
                    'nama' => $karyawan->nama_karyawan,
                    'id_karyawan' => $karyawan->id_karyawan,
                    'departemen' => $karyawan->departemen?->nama_departemen,
                    'jabatan' => $karyawan->jabatan?->nama_jabatan,
                    'kode_shift' => $jadwal->kode_shift,
                    'nama_shift' => $jadwal->shift?->nama_shift,
                    'jam_shift' => substr($jadwal->shift?->jam_masuk ?? '', 0, 5) . ' - ' . substr($jadwal->shift?->jam_pulang ?? '', 0, 5),
                    'waktu' => $now->format('H:i:s') . ' WIB',
                    'tanggal' => $now->translatedFormat('l, d F Y'),
                    'status' => $statusLabel,
                    'color' => $statusColor,
                ];

                // Notifikasi ke supervisor
                if ($karyawan->id_departemen) {
                    NotificationService::kirimKeDepartemen(
                        $karyawan->id_departemen,
                        'SUPERVISOR',
                        "Presensi Masuk: {$karyawan->nama_karyawan}",
                        "Operator {$karyawan->nama_karyawan} telah absen masuk pada {$now->format('H:i:s')} WIB ({$statusLabel}).",
                        '/absensi/koreksi'
                    );
                }
            } else {
                // Cari catatan absensi aktif yang belum checkout (atau absensi hari ini)
                $absensi = Absensi::with(['jadwal.shift', 'karyawan.departemen', 'karyawan.jabatan'])
                    ->where('id_karyawan', $karyawan->id_karyawan)
                    ->whereNotNull('waktu_masuk')
                    ->whereNull('waktu_pulang')
                    ->latest('tanggal_shift')
                    ->first();

                if (!$absensi) {
                    $absensiHariIni = Absensi::with(['jadwal.shift', 'karyawan.departemen'])
                        ->where('id_karyawan', $karyawan->id_karyawan)
                        ->where('tanggal_shift', $today)
                        ->first();

                    if ($absensiHariIni && $absensiHariIni->waktu_pulang) {
                        throw new Exception("Anda sudah melakukan absensi pulang pada pukul {$absensiHariIni->waktu_pulang->format('H:i:s')} WIB.");
                    }

                    throw new Exception("Belum ada catatan absen masuk untuk shift ini. Silakan lakukan Absen Masuk terlebih dahulu.");
                }

                $absensi = $service->catatAbsenPulang($absensi, $now, 'KIOSK');

                $jamKerja = $absensi->menit_kerja ? round($absensi->menit_kerja / 60, 1) . ' Jam' : '-';
                $statusLabel = $absensi->pulang_cepat ? "PULANG CEPAT (Total Kerja: {$jamKerja})" : "SELESAI SHIFT (Total Kerja: {$jamKerja})";
                $statusColor = $absensi->pulang_cepat ? 'amber' : 'sky';

                $this->konfirmasi = [
                    'tipe' => 'PULANG',
                    'nama' => $karyawan->nama_karyawan,
                    'id_karyawan' => $karyawan->id_karyawan,
                    'departemen' => $karyawan->departemen?->nama_departemen,
                    'jabatan' => $karyawan->jabatan?->nama_jabatan,
                    'kode_shift' => $absensi->jadwal?->kode_shift ?? 'S1',
                    'nama_shift' => $absensi->jadwal?->shift?->nama_shift ?? 'Shift Kerja',
                    'jam_shift' => substr($absensi->jadwal?->shift?->jam_masuk ?? '', 0, 5) . ' - ' . substr($absensi->jadwal?->shift?->jam_pulang ?? '', 0, 5),
                    'waktu' => $now->format('H:i:s') . ' WIB',
                    'tanggal' => $now->translatedFormat('l, d F Y'),
                    'status' => $statusLabel,
                    'color' => $statusColor,
                ];
            }

            // Reset field PIN dan ID untuk keamanan
            $this->pin = '';
            $this->id_karyawan = '';
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        $now = now('Asia/Jakarta');
        $shifts = Shift::where('is_aktif', true)->orderBy('jam_masuk')->get();

        // Tentukan shift yang sedang berjalan berdasarkan jam sekarang
        $currentTime = $now->format('H:i:s');
        $currentShift = null;
        foreach ($shifts as $s) {
            if ($s->is_libur) continue;
            if ($s->lintas_hari) {
                if ($currentTime >= $s->jam_masuk || $currentTime <= $s->jam_pulang) {
                    $currentShift = $s;
                    break;
                }
            } else {
                if ($currentTime >= $s->jam_masuk && $currentTime <= $s->jam_pulang) {
                    $currentShift = $s;
                    break;
                }
            }
        }

        // Demo sample employees per department for quick punch in/out
        $departemenList = Departemen::with(['karyawan' => function($q) {
            $q->where('status_karyawan', 'AKTIF')
              ->with('jabatan')
              ->orderBy('id_karyawan');
        }])->orderBy('id_departemen')->get();

        // Presensi terkini (live stream feed)
        $recentAbsensi = Absensi::with(['karyawan.departemen', 'jadwal.shift'])
            ->whereDate('tanggal_shift', $now->toDateString())
            ->whereNotNull('waktu_masuk')
            ->latest('updated_at')
            ->take(3)
            ->get();

        return view('livewire.kiosk-presensi', [
            'currentTime' => $now->format('H:i:s'),
            'currentDate' => $now->translatedFormat('l, d F Y'),
            'currentShift' => $currentShift,
            'shifts' => $shifts,
            'departemenList' => $departemenList,
            'recentAbsensi' => $recentAbsensi,
        ]);
    }
}
