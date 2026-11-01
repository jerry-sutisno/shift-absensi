<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\GrupKaryawan;
use App\Models\PolaRotasi;
use App\Models\JadwalShift;
use App\Models\Absensi;
use App\Models\Pengajuan;
use App\Models\TukarShift;
use App\Models\AuditLog;
use App\Models\Pengaturan;
use App\Services\ScheduleGeneratorService;
use App\Services\AttendanceService;
use App\Services\TukarShiftService;
use App\Livewire\KoreksiAbsensiManager;
use App\Livewire\PengajuanApprovalHub;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;
use InvalidArgumentException;
use Livewire\Livewire;

class PlanKeyScenariosTest extends TestCase
{
    protected User $adminHr;
    protected User $spvPrd;
    protected User $spvLgs;
    protected User $oprPrd1;
    protected User $oprPrd2;
    protected User $oprLgs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminHr = User::where('id_karyawan', 'EMP-0001')->firstOrFail();
        $this->spvPrd = User::where('id_karyawan', 'EMP-0009')->firstOrFail();
        $this->spvLgs = User::where('id_karyawan', 'EMP-0010')->firstOrFail();
        $this->oprPrd1 = User::where('id_karyawan', 'EMP-0013')->firstOrFail();
        $this->oprPrd2 = User::where('id_karyawan', 'EMP-0014')->firstOrFail();
        $this->oprLgs = User::where('id_karyawan', 'EMP-0025')->firstOrFail();
    }

    /**
     * Skenario 1 (docs/PLAN.md 8.2 No 1):
     * Generate pola 4 grup selama 30 hari.
     * Hasil yang diharapkan: Setiap hari S1, S2, S3 terisi satu grup dan satu grup OFF.
     */
    public function test_skenario_1_generate_pola_4_grup_selama_30_hari_setiap_hari_s1_s2_s3_terisi_satu_grup_dan_satu_grup_off(): void
    {
        $dept = Departemen::where('kode_departemen', 'PRD')->firstOrFail();
        $pola = PolaRotasi::where('panjang_siklus_hari', 8)->firstOrFail();
        $service = app(ScheduleGeneratorService::class);

        // Generate untuk November 2026 (tepat 30 hari: 2026-11-01 s/d 2026-11-30)
        $result = $service->generateBulanan(
            $dept->id_departemen,
            2026,
            11,
            $pola->id_pola,
            '2026-11-01'
        );

        $this->assertTrue($result['status']);
        $this->assertGreaterThan(0, $result['generated']);

        // Ambil ID 4 grup di Produksi (Grup A, B, C, D)
        $grupIds = GrupKaryawan::whereIn('nama_grup', ['Grup A', 'Grup B', 'Grup C', 'Grup D'])
            ->pluck('id_grup')
            ->toArray();
        $this->assertCount(4, $grupIds);

        $startDate = Carbon::create(2026, 11, 1);
        $endDate = Carbon::create(2026, 11, 30);
        $period = CarbonPeriod::create($startDate, $endDate);

        $this->assertEquals(30, $period->count(), 'Periode harus tepat 30 hari');

        foreach ($period as $date) {
            $dateStr = $date->toDateString();

            // Ambil shift per grup pada tanggal ini
            $schedules = JadwalShift::with('karyawan')
                ->where('tanggal', $dateStr)
                ->whereHas('karyawan', function ($q) use ($dept, $grupIds) {
                    $q->where('id_departemen', $dept->id_departemen)
                      ->whereIn('id_grup', $grupIds);
                })
                ->get();

            // Kelompokkan kode_shift per grup unik
            $shiftsByGrup = [];
            foreach ($schedules as $jadwal) {
                $shiftsByGrup[$jadwal->karyawan->id_grup] = $jadwal->kode_shift;
            }

            // Pastikan seluruh 4 grup memiliki jadwal pada tanggal ini
            $this->assertCount(4, $shiftsByGrup, "Tanggal {$dateStr} harus mencakup 4 grup");

            // Hitung frekuensi shift per hari di antara 4 grup
            $shiftCounts = array_count_values(array_values($shiftsByGrup));

            $this->assertEquals(1, $shiftCounts['S1'] ?? 0, "Pada tanggal {$dateStr}, S1 harus terisi tepat satu grup");
            $this->assertEquals(1, $shiftCounts['S2'] ?? 0, "Pada tanggal {$dateStr}, S2 harus terisi tepat satu grup");
            $this->assertEquals(1, $shiftCounts['S3'] ?? 0, "Pada tanggal {$dateStr}, S3 harus terisi tepat satu grup");
            $this->assertEquals(1, $shiftCounts['OFF'] ?? 0, "Pada tanggal {$dateStr}, OFF harus terisi tepat satu grup");
        }
    }

    /**
     * Skenario 2 (docs/PLAN.md 8.2 No 2):
     * Karyawan tanpa grup.
     * Hasil yang diharapkan: GS Senin sampai Jumat, Sabtu dan Minggu OFF.
     */
    public function test_skenario_2_karyawan_tanpa_grup_gs_senin_sampai_jumat_sabtu_minggu_off(): void
    {
        // Karyawan non-rotasi (id_grup = null), misalnya SPV atau HOD Produksi
        $karyawanNonGrup = Karyawan::where('id_departemen', $this->spvPrd->karyawan->id_departemen)
            ->whereNull('id_grup')
            ->firstOrFail();

        $startDate = Carbon::create(2026, 11, 1);
        $endDate = Carbon::create(2026, 11, 30);
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $jadwal = JadwalShift::where('id_karyawan', $karyawanNonGrup->id_karyawan)
                ->where('tanggal', $dateStr)
                ->first();

            $this->assertNotNull($jadwal, "Jadwal untuk {$karyawanNonGrup->nama_karyawan} pada {$dateStr} harus digenerate");

            if ($date->isWeekend()) {
                // Sabtu & Minggu wajib OFF
                $this->assertEquals('OFF', $jadwal->kode_shift, "Karyawan non-grup pada akhir pekan ({$dateStr}) harus OFF");
            } else {
                // Senin s/d Jumat wajib GS
                $this->assertEquals('GS', $jadwal->kode_shift, "Karyawan non-grup pada hari kerja ({$dateStr}) harus General Shift (GS)");
            }
        }
    }

    /**
     * Skenario 3 (docs/PLAN.md 8.2 No 3):
     * S3: masuk 22:45 tanggal 1, pulang 07:05 tanggal 2.
     * Hasil yang diharapkan: Satu record dengan tanggal_shift 1, status HADIR.
     */
    public function test_skenario_3_s3_masuk_2245_tgl1_pulang_0705_tgl2_satu_record_tanggal_shift_1_status_hadir(): void
    {
        $karyawan = $this->oprPrd1->karyawan;
        $tgl1 = '2026-10-01';
        $tgl2 = '2026-10-02';

        // Jadwal shift S3 pada tanggal 1 (23:00 - 07:00 lintas hari)
        $jadwalS3 = JadwalShift::updateOrCreate(
            ['id_karyawan' => $karyawan->id_karyawan, 'tanggal' => $tgl1],
            ['kode_shift' => 'S3', 'sumber' => 'MANUAL', 'status' => 'PUBLISHED']
        );

        // Hapus data absensi sebelumnya jika ada
        Absensi::where('id_karyawan', $karyawan->id_karyawan)->whereIn('tanggal_shift', [$tgl1, $tgl2])->delete();

        $service = app(AttendanceService::class);

        // Tap masuk pada 2026-10-01 22:45:00 (15 menit sebelum 23:00, jendela masuk buka 60 menit)
        $waktuMasuk = Carbon::parse('2026-10-01 22:45:00');
        $absensi = $service->catatAbsenMasuk($jadwalS3, $waktuMasuk, 'KIOSK');

        // Tap pulang pada 2026-10-02 07:05:00 (lewat tengah malam di hari berikutnya)
        $waktuPulang = Carbon::parse('2026-10-02 07:05:00');
        $service->catatAbsenPulang($absensi, $waktuPulang, 'KIOSK');

        // Verifikasi di database: HANYA ada SATU record untuk shift ini
        $records = Absensi::where('id_karyawan', $karyawan->id_karyawan)
            ->where('id_jadwal', $jadwalS3->id_jadwal)
            ->get();

        $this->assertCount(1, $records, 'Harus tersimpan tepat SATU record absensi untuk shift lintas hari');

        $record = $records->first();
        $this->assertEquals($tgl1, $record->tanggal_shift->toDateString(), 'tanggal_shift harus tetap tanggal MULAI shift (tanggal 1)');
        $this->assertEquals('HADIR', $record->status, 'Status absensi harus HADIR (tidak terlambat)');
        $this->assertEquals(0, $record->menit_terlambat, 'Menit terlambat harus 0 karena datang sebelum jam masuk');
        $this->assertFalse($record->pulang_cepat, 'Pulang pukul 07:05 tidak dihitung pulang cepat');
        $this->assertEquals(500, $record->menit_kerja, 'Durasi kerja dari 22:45 s/d 07:05 adalah 500 menit (8 jam 20 menit)');

        // Pastikan tidak ada record terpisah yang tercipta di tanggal 2
        $recordTgl2 = Absensi::where('id_karyawan', $karyawan->id_karyawan)
            ->where('tanggal_shift', $tgl2)
            ->first();
        $this->assertNull($recordTgl2, 'Tidak boleh ada record absensi terpisah di tanggal 2 untuk shift S3 tanggal 1');
    }

    /**
     * Skenario 4 (docs/PLAN.md 8.2 No 4):
     * S1 (07:00): masuk 07:25 dengan toleransi 10 menit.
     * Hasil yang diharapkan: Status TERLAMBAT, menit_terlambat 25.
     */
    public function test_skenario_4_s1_0700_masuk_0725_toleransi_10_menit_status_terlambat_menit_terlambat_25(): void
    {
        $karyawan = $this->oprPrd2->karyawan;
        $tgl = '2026-10-05';

        $jadwalS1 = JadwalShift::updateOrCreate(
            ['id_karyawan' => $karyawan->id_karyawan, 'tanggal' => $tgl],
            ['kode_shift' => 'S1', 'sumber' => 'MANUAL', 'status' => 'PUBLISHED']
        );

        Absensi::where('id_karyawan', $karyawan->id_karyawan)->where('tanggal_shift', $tgl)->delete();

        // Pastikan pengaturan toleransi adalah 10 menit
        Pengaturan::updateOrCreate(
            ['kunci' => 'toleransi_terlambat_menit'],
            ['nilai' => '10', 'keterangan' => 'Toleransi keterlambatan dalam menit']
        );

        $service = app(AttendanceService::class);
        $waktuMasuk = Carbon::parse('2026-10-05 07:25:00');

        $absensi = $service->catatAbsenMasuk($jadwalS1, $waktuMasuk, 'KIOSK');

        $this->assertEquals('TERLAMBAT', $absensi->status, 'Status harus TERLAMBAT karena masuk melebihi batas 07:10');
        // BR-05: Menit keterlambatan dihitung dari jam masuk resmi shift (07:00:00), bukan dari akhir jendela toleransi
        $this->assertEquals(25, $absensi->menit_terlambat, 'Menit terlambat harus 25 (07:25 - 07:00)');
    }

    /**
     * Skenario 5 (docs/PLAN.md 8.2 No 5):
     * Operator absen pada hari OFF.
     * Hasil yang diharapkan: Ditolak dengan pesan jelas.
     */
    public function test_skenario_5_operator_absen_pada_hari_off_ditolak_dengan_pesan_jelas(): void
    {
        $karyawan = $this->oprPrd1->karyawan;
        $tglOff = '2026-10-06';

        $jadwalOff = JadwalShift::updateOrCreate(
            ['id_karyawan' => $karyawan->id_karyawan, 'tanggal' => $tglOff],
            ['kode_shift' => 'OFF', 'sumber' => 'POLA', 'status' => 'PUBLISHED']
        );

        Absensi::where('id_karyawan', $karyawan->id_karyawan)->where('tanggal_shift', $tglOff)->delete();

        $service = app(AttendanceService::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Shift OFF tidak memerlukan absensi.');

        try {
            $service->catatAbsenMasuk($jadwalOff, Carbon::parse('2026-10-06 08:00:00'), 'KIOSK');
        } finally {
            // Pastikan tidak ada data yang masuk ke tabel absensi
            $this->assertDatabaseMissing('absensi', [
                'id_karyawan' => $karyawan->id_karyawan,
                'tanggal_shift' => $tglOff,
            ]);
        }
    }

    /**
     * Skenario 6 (docs/PLAN.md 8.2 No 6):
     * Tidak absen sampai batas; kasus lain dengan cuti disetujui.
     * Hasil yang diharapkan: ALPA; kasus cuti menjadi CUTI.
     */
    public function test_skenario_6_tidak_absen_sampai_batas_alpa_vs_cuti_disetujui(): void
    {
        $tglUji = '2026-10-07';

        // 1. Karyawan 1 (oprPrd1): Jadwal S1, tidak absen, TIDAK mengajukan cuti
        $j1 = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprPrd1->id_karyawan, 'tanggal' => $tglUji],
            ['kode_shift' => 'S1', 'sumber' => 'POLA', 'status' => 'PUBLISHED']
        );

        // 2. Karyawan 2 (oprPrd2): Jadwal S1, tidak absen, MEMILIKI cuti yang DISETUJUI
        $j2 = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprPrd2->id_karyawan, 'tanggal' => $tglUji],
            ['kode_shift' => 'S1', 'sumber' => 'POLA', 'status' => 'PUBLISHED']
        );

        // Hapus absensi sebelumnya
        Absensi::whereIn('id_karyawan', [$this->oprPrd1->id_karyawan, $this->oprPrd2->id_karyawan])
            ->where('tanggal_shift', $tglUji)
            ->delete();

        // Buat pengajuan cuti yang disetujui untuk oprPrd2
        Pengajuan::where('id_karyawan', $this->oprPrd2->id_karyawan)->where('tgl_mulai', $tglUji)->delete();
        Pengajuan::create([
            'id_karyawan' => $this->oprPrd2->id_karyawan,
            'jenis' => 'CUTI',
            'tgl_mulai' => $tglUji,
            'tgl_selesai' => $tglUji,
            'alasan' => 'Cuti tahunan keperluan keluarga',
            'status' => 'DISETUJUI',
            'tahap' => 'SELESAI',
            'id_approver' => $this->spvPrd->id_user,
            'keputusan_at' => now('Asia/Jakarta'),
        ]);

        $service = app(AttendanceService::class);
        $totalProcessed = $service->prosesAlpaOtomatis($tglUji);

        $this->assertGreaterThanOrEqual(2, $totalProcessed);

        // Kasus 1: Tanpa cuti menjadi ALPA
        $absensi1 = Absensi::where('id_karyawan', $this->oprPrd1->id_karyawan)
            ->where('tanggal_shift', $tglUji)
            ->firstOrFail();
        $this->assertEquals('ALPA', $absensi1->status, 'Karyawan tanpa absensi dan tanpa izin resmi harus ditandai ALPA');

        // Kasus 2: Dengan cuti disetujui menjadi CUTI
        $absensi2 = Absensi::where('id_karyawan', $this->oprPrd2->id_karyawan)
            ->where('tanggal_shift', $tglUji)
            ->firstOrFail();
        $this->assertEquals('CUTI', $absensi2->status, 'Karyawan yang memiliki persetujuan cuti resmi harus ditandai CUTI');
    }

    /**
     * Skenario 7 (docs/PLAN.md 8.2 No 7):
     * Tukar shift yang membuat S3 disusul S1 keesokan harinya.
     * Hasil yang diharapkan: Ditolak karena istirahat kurang dari 8 jam.
     */
    public function test_skenario_7_tukar_shift_yang_membuat_s3_disusul_s1_keesokan_harinya_ditolak_karena_istirahat_kurang_8_jam(): void
    {
        $tgl1 = '2026-10-08';
        $tgl2 = '2026-10-09';

        // Pemohon (oprPrd1):
        // Hari 1: Shift S3 (23:00 s/d 07:00 keesokan harinya di tanggal 2)
        // Hari 2: Jadwal awal OFF
        JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprPrd1->id_karyawan, 'tanggal' => $tgl1],
            ['kode_shift' => 'S3', 'sumber' => 'POLA', 'status' => 'PUBLISHED']
        );
        $jadwalPemohonHari2 = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprPrd1->id_karyawan, 'tanggal' => $tgl2],
            ['kode_shift' => 'OFF', 'sumber' => 'POLA', 'status' => 'PUBLISHED']
        );

        // Rekan pengganti (oprPrd2):
        // Hari 2: Shift S1 (07:00 s/d 15:00)
        $jadwalPenggantiHari2 = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprPrd2->id_karyawan, 'tanggal' => $tgl2],
            ['kode_shift' => 'S1', 'sumber' => 'POLA', 'status' => 'PUBLISHED']
        );

        $service = app(TukarShiftService::class);

        // Pemohon ingin menukar jadwal Hari 2 miliknya (OFF) dengan jadwal Hari 2 milik rekannya (S1).
        // Konsekuensi: Pemohon akan menyelesaikan S3 pukul 07:00 dan langsung bekerja S1 pukul 07:00 (istirahat 0 jam).
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('minimal 8 jam sesuai regulasi Depnaker');

        $service->ajukanTukar(
            $this->oprPrd1->karyawan,
            $this->oprPrd2->id_karyawan,
            $jadwalPemohonHari2->id_jadwal,
            $jadwalPenggantiHari2->id_jadwal,
            'Tukar shift berisiko istirahat 0 jam'
        );
    }

    /**
     * Skenario 8 (docs/PLAN.md 8.2 No 8):
     * Supervisor departemen A membuka data departemen B.
     * Hasil yang diharapkan: Ditolak (403).
     */
    public function test_skenario_8_supervisor_departemen_a_membuka_data_departemen_b_ditolak_403(): void
    {
        $tgl = '2026-10-10';

        // Data di Departemen Logistik (LGS)
        $jadwalLgs = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprLgs->id_karyawan, 'tanggal' => $tgl],
            ['kode_shift' => 'GS', 'sumber' => 'MANUAL', 'status' => 'PUBLISHED']
        );

        $absensiLgs = Absensi::updateOrCreate(
            ['id_karyawan' => $this->oprLgs->id_karyawan, 'tanggal_shift' => $tgl],
            ['id_jadwal' => $jadwalLgs->id_jadwal, 'status' => 'HADIR']
        );

        $pengajuanLgs = Pengajuan::updateOrCreate(
            ['id_karyawan' => $this->oprLgs->id_karyawan, 'tgl_mulai' => $tgl],
            [
                'jenis' => 'IZIN',
                'tgl_selesai' => $tgl,
                'alasan' => 'Izin keperluan dinas logistik',
                'status' => 'DIAJUKAN',
                'tahap' => 'SUPERVISOR',
            ]
        );

        // 1. Supervisor Produksi ($spvPrd) menguji akses ke data karyawan Logistik
        $this->assertFalse(
            Gate::forUser($this->spvPrd)->allows('view', $jadwalLgs),
            'Supervisor Produksi tidak boleh diizinkan melihat jadwal karyawan Logistik'
        );

        $this->assertFalse(
            Gate::forUser($this->spvPrd)->allows('view', $absensiLgs),
            'Supervisor Produksi tidak boleh diizinkan melihat absensi karyawan Logistik'
        );

        $this->assertFalse(
            Gate::forUser($this->spvPrd)->allows('view', $pengajuanLgs),
            'Supervisor Produksi tidak boleh diizinkan melihat pengajuan karyawan Logistik'
        );

        // 2. Supervisor Produksi menguji otorisasi (harus melempar AuthorizationException dengan status 403)
        $this->expectException(AuthorizationException::class);
        Gate::forUser($this->spvPrd)->authorize('view', $jadwalLgs);
    }

    /**
     * Skenario 8 Lanjutan:
     * Verifikasi otorisasi pada komponen Livewire untuk Supervisor lintas departemen.
     */
    public function test_skenario_8_livewire_koreksi_absensi_lintas_departemen_ditolak_403(): void
    {
        $tgl = '2026-10-11';
        $jadwalLgs = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprLgs->id_karyawan, 'tanggal' => $tgl],
            ['kode_shift' => 'GS', 'sumber' => 'MANUAL', 'status' => 'PUBLISHED']
        );

        $absensiLgs = Absensi::updateOrCreate(
            ['id_karyawan' => $this->oprLgs->id_karyawan, 'tanggal_shift' => $tgl],
            ['id_jadwal' => $jadwalLgs->id_jadwal, 'status' => 'HADIR']
        );

        Livewire::actingAs($this->spvPrd)
            ->test(KoreksiAbsensiManager::class)
            ->call('openKoreksiModal', $absensiLgs->id_absensi)
            ->assertForbidden();
    }

    /**
     * Skenario 9 (docs/PLAN.md 8.2 No 9):
     * Koreksi absensi tanpa alasan, lalu dengan alasan.
     * Hasil yang diharapkan: Pertama ditolak, kedua berhasil dan tercatat di audit_log.
     */
    public function test_skenario_9_koreksi_absensi_tanpa_alasan_lalu_dengan_alasan_tercatat_di_audit_log(): void
    {
        $tgl = '2026-10-12';
        $karyawan = $this->oprPrd1->karyawan;

        $jadwal = JadwalShift::updateOrCreate(
            ['id_karyawan' => $karyawan->id_karyawan, 'tanggal' => $tgl],
            ['kode_shift' => 'S1', 'sumber' => 'POLA', 'status' => 'PUBLISHED']
        );

        $absensi = Absensi::updateOrCreate(
            ['id_karyawan' => $karyawan->id_karyawan, 'tanggal_shift' => $tgl],
            ['id_jadwal' => $jadwal->id_jadwal, 'status' => 'ALPA']
        );

        $service = app(AttendanceService::class);

        // Percobaan 1: Tanpa alasan koreksi -> WAJIB DITOLAK
        $ditolak = false;
        try {
            $service->koreksiAbsensi(
                $absensi->id_absensi,
                ['status' => 'HADIR'],
                $this->spvPrd,
                '   ' // String kosong / hanya spasi
            );
        } catch (InvalidArgumentException $e) {
            $ditolak = true;
            $this->assertStringContainsString('Alasan koreksi absensi wajib diisi', $e->getMessage());
        }

        $this->assertTrue($ditolak, 'Koreksi absensi tanpa alasan harus ditolak');
        $this->assertEquals('ALPA', $absensi->fresh()->status, 'Status absensi tidak boleh berubah saat koreksi gagal');

        // Percobaan 2: Dengan alasan koreksi yang jelas -> BERHASIL & AUDIT LOG TERTULIS
        $alasanValid = 'Kamera kiosk error saat pergantian shift pagi, verifikasi visual hadir oleh SPV Gilang';
        $hasil = $service->koreksiAbsensi(
            $absensi->id_absensi,
            [
                'waktu_masuk' => '2026-10-12 06:55:00',
                'waktu_pulang' => '2026-10-12 15:05:00',
                'status' => 'HADIR',
                'menit_terlambat' => 0,
            ],
            $this->spvPrd,
            $alasanValid
        );

        $this->assertEquals('HADIR', $hasil->status, 'Status absensi berhasil dikoreksi menjadi HADIR');
        $this->assertEquals($this->spvPrd->id_user, $hasil->dikoreksi_oleh);
        $this->assertEquals($alasanValid, $hasil->catatan);

        // Verifikasi bahwa audit_log mencatat perubahan dengan data lama dan baru (BR-12)
        $audit = AuditLog::where('aksi', 'KOREKSI_ABSENSI')
            ->where('tabel', 'absensi')
            ->where('id_record', (string) $absensi->id_absensi)
            ->latest('id_log')
            ->first();

        $this->assertNotNull($audit, 'Koreksi absensi wajib menulis audit_log');
        $this->assertEquals('ALPA', $audit->data_lama['status'] ?? null, 'Audit log data lama harus memuat status ALPA');
        $this->assertEquals('HADIR', $audit->data_baru['status'] ?? null, 'Audit log data baru harus memuat status HADIR');
        $this->assertEquals($alasanValid, $audit->data_baru['catatan'] ?? null, 'Audit log data baru harus memuat alasan koreksi');
    }

    /**
     * Skenario 10 (docs/PLAN.md 8.2 No 10):
     * Operator membuka jadwal berstatus DRAFT.
     * Hasil yang diharapkan: Tidak terlihat.
     */
    public function test_skenario_10_operator_membuka_jadwal_berstatus_draft_tidak_terlihat(): void
    {
        $karyawan = $this->oprPrd1->karyawan;
        $tglDraft = '2026-11-20';

        // Jadwal berstatus DRAFT milik operator
        $draftJadwal = JadwalShift::updateOrCreate(
            ['id_karyawan' => $karyawan->id_karyawan, 'tanggal' => $tglDraft],
            ['kode_shift' => 'S1', 'sumber' => 'POLA', 'status' => 'DRAFT']
        );

        // 1. Policy check: Operator TIDAK diizinkan melihat jadwal berstatus DRAFT
        $this->assertFalse(
            Gate::forUser($this->oprPrd1)->allows('view', $draftJadwal),
            'Operator dilarang melihat jadwal miliknya yang masih berstatus DRAFT (Rule 8)'
        );

        // 2. Query scope check: scopeVisibleFor menyaring keluar jadwal DRAFT untuk operator
        $visibleScheduleIds = JadwalShift::visibleFor($this->oprPrd1)
            ->pluck('id_jadwal')
            ->toArray();

        $this->assertNotContains(
            $draftJadwal->id_jadwal,
            $visibleScheduleIds,
            'Jadwal DRAFT tidak boleh muncul dalam daftar jadwal yang terlihat oleh Operator'
        );

        // 3. Setelah jadwal di-PUBLISH, operator baru dapat melihatnya
        $draftJadwal->update(['status' => 'PUBLISHED']);

        $this->assertTrue(
            Gate::forUser($this->oprPrd1)->allows('view', $draftJadwal->fresh()),
            'Operator harus dapat melihat jadwal setelah statusnya PUBLISHED'
        );

        $visibleScheduleIdsAfterPublish = JadwalShift::visibleFor($this->oprPrd1)
            ->pluck('id_jadwal')
            ->toArray();

        $this->assertContains(
            $draftJadwal->id_jadwal,
            $visibleScheduleIdsAfterPublish,
            'Jadwal PUBLISHED harus muncul dalam daftar jadwal yang terlihat oleh Operator'
        );
    }
}
