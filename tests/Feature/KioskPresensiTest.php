<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\JadwalShift;
use App\Models\Absensi;
use App\Models\Shift;
use Livewire\Livewire;
use App\Livewire\KioskPresensi;
use Carbon\Carbon;

class KioskPresensiTest extends TestCase
{
    protected User $oprPrd;
    protected Karyawan $karyawanOpr;

    protected function setUp(): void
    {
        parent::setUp();

        $this->oprPrd = User::where('id_karyawan', 'EMP-0013')->firstOrFail();
        $this->karyawanOpr = $this->oprPrd->karyawan;
    }

    /**
     * 1. Halaman Kiosk dapat diakses secara publik (tanpa login session) oleh tablet pabrik.
     */
    public function test_kiosk_page_is_accessible_publicly(): void
    {
        $response = $this->get('/kiosk');
        $response->assertStatus(200);
        $response->assertSee('KIOSK MODE');
        $response->assertSee('ABSEN MASUK');
        $response->assertSee('ABSEN PULANG');
    }

    /**
     * 2. Keypad interaktif Kiosk mengisi PIN dan dapat di-reset.
     */
    public function test_kiosk_keypad_interaction(): void
    {
        Livewire::test(KioskPresensi::class)
            ->set('id_karyawan', 'EMP-0013')
            ->call('pressKey', '1')
            ->call('pressKey', '2')
            ->call('pressKey', '3')
            ->assertSet('pin', '123')
            ->call('backspacePin')
            ->assertSet('pin', '12')
            ->call('clearPin')
            ->assertSet('pin', '');
    }

    /**
     * 3. Absen dengan PIN salah akan ditolak dengan pesan kesalahan.
     */
    public function test_kiosk_rejects_wrong_pin(): void
    {
        Livewire::test(KioskPresensi::class)
            ->set('id_karyawan', 'EMP-0013')
            ->set('pin', '999999') // PIN salah
            ->call('prosesAbsenMasuk')
            ->assertSet('errorMessage', 'PIN Kiosk tidak sesuai. Silakan hubungi Supervisor / HR jika lupa PIN.')
            ->assertSet('konfirmasi', null);
    }

    /**
     * 4. Absen Masuk berhasil dengan ID Karyawan dan PIN valid jika ada jadwal.
     */
    public function test_kiosk_absen_masuk_successful_with_valid_pin(): void
    {
        $today = now('Asia/Jakarta')->toDateString();

        // Pastikan ada shift aktif hari ini untuk EMP-0013
        $jadwal = JadwalShift::updateOrCreate(
            [
                'id_karyawan' => $this->karyawanOpr->id_karyawan,
                'tanggal' => $today,
            ],
            [
                'kode_shift' => 'S1',
                'status' => 'PUBLISHED',
                'sumber' => 'POLA',
            ]
        );

        // Hapus absensi hari ini jika sudah ada dari tes lain
        Absensi::where('id_karyawan', $this->karyawanOpr->id_karyawan)
            ->where('tanggal_shift', $today)
            ->delete();

        $testable = Livewire::test(KioskPresensi::class)
            ->set('id_karyawan', 'EMP-0013')
            ->set('pin', '123456') // PIN default seeder
            ->call('prosesAbsenMasuk')
            ->assertSet('errorMessage', null);

        $this->assertNotNull($testable->get('konfirmasi'));

        $this->assertDatabaseHas('absensi', [
            'id_karyawan' => 'EMP-0013',
            'tanggal_shift' => $today,
            'metode_masuk' => 'KIOSK',
        ]);
    }

    /**
     * 5. Absen Pulang berhasil mencatat jam pulang dan menghitung menit kerja.
     */
    public function test_kiosk_absen_pulang_successful(): void
    {
        $today = now('Asia/Jakarta')->toDateString();

        $absensi = Absensi::where('id_karyawan', 'EMP-0013')
            ->where('tanggal_shift', $today)
            ->first();

        if (!$absensi) {
            $jadwal = JadwalShift::firstOrCreate([
                'id_karyawan' => 'EMP-0013',
                'tanggal' => $today,
                'kode_shift' => 'S1',
                'status' => 'PUBLISHED',
            ]);

            $absensi = Absensi::create([
                'id_jadwal' => $jadwal->id_jadwal,
                'id_karyawan' => 'EMP-0013',
                'tanggal_shift' => $today,
                'waktu_masuk' => now('Asia/Jakarta')->subHours(8),
                'metode_masuk' => 'KIOSK',
                'status' => 'HADIR',
            ]);
        } else {
            $absensi->update([
                'waktu_masuk' => now('Asia/Jakarta')->subHours(8),
                'waktu_pulang' => null,
            ]);
        }

        $testable = Livewire::test(KioskPresensi::class)
            ->set('id_karyawan', 'EMP-0013')
            ->set('pin', '123456')
            ->call('prosesAbsenPulang')
            ->assertSet('errorMessage', null);

        $this->assertNotNull($testable->get('konfirmasi'));

        $absensi->refresh();
        $this->assertNotNull($absensi->waktu_pulang);
        $this->assertEquals('KIOSK', $absensi->metode_pulang);
        $this->assertNotNull($absensi->menit_kerja);
    }
}
