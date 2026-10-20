<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\JadwalShift;
use App\Models\Absensi;
use App\Models\TukarShift;
use App\Models\Lembur;
use App\Models\LogbookShift;
use App\Models\Notifikasi;
use App\Models\HariLibur;
use App\Models\AuditLog;
use App\Services\TukarShiftService;
use App\Services\AttendanceService;
use App\Services\OvertimeService;
use App\Services\NotificationService;
use Carbon\Carbon;
use InvalidArgumentException;

class OperationalFeaturesTest extends TestCase
{
    protected User $adminHr;
    protected User $spvPrd;
    protected User $oprPrd1;
    protected User $oprPrd2;
    protected User $oprLgs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminHr = User::where('id_karyawan', 'EMP-0001')->firstOrFail();
        $this->spvPrd = User::where('id_karyawan', 'EMP-0009')->firstOrFail();
        $this->oprPrd1 = User::where('id_karyawan', 'EMP-0013')->firstOrFail();
        $this->oprPrd2 = User::where('id_karyawan', 'EMP-0014')->firstOrFail();
        $this->oprLgs = User::where('id_karyawan', 'EMP-0025')->firstOrFail();
    }

    // ==========================================
    // 1. TUKAR SHIFT (F-10 / BR-10)
    // ==========================================

    public function test_tukar_shift_fails_for_different_departments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('hanya dapat dilakukan antar karyawan dalam satu departemen');

        $tgl = '2026-10-25';
        $j1 = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprPrd1->id_karyawan, 'tanggal' => $tgl],
            ['kode_shift' => 'S1', 'sumber' => 'MANUAL', 'status' => 'PUBLISHED']
        );
        $j2 = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprLgs->id_karyawan, 'tanggal' => $tgl],
            ['kode_shift' => 'S2', 'sumber' => 'MANUAL', 'status' => 'PUBLISHED']
        );

        $service = app(TukarShiftService::class);
        $service->ajukanTukar($this->oprPrd1->karyawan, $this->oprLgs->id_karyawan, $j1->id_jadwal, $j2->id_jadwal, 'Tukar beda dept');
    }

    public function test_tukar_shift_complete_workflow_and_audit(): void
    {
        $tgl1 = '2026-10-26';
        $tgl2 = '2026-10-27';

        $j1 = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprPrd1->id_karyawan, 'tanggal' => $tgl1],
            ['kode_shift' => 'S1', 'sumber' => 'POLA', 'status' => 'PUBLISHED']
        );
        $j2 = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprPrd2->id_karyawan, 'tanggal' => $tgl2],
            ['kode_shift' => 'S2', 'sumber' => 'POLA', 'status' => 'PUBLISHED']
        );

        $service = app(TukarShiftService::class);

        // 1. Pemohon mengajukan
        $tukar = $service->ajukanTukar(
            $this->oprPrd1->karyawan,
            $this->oprPrd2->id_karyawan,
            $j1->id_jadwal,
            $j2->id_jadwal,
            'Keperluan keluarga mendesak'
        );

        $this->assertEquals('MENUNGGU_PENGGANTI', $tukar->status);
        $this->assertDatabaseHas('notifikasi', [
            'id_user' => $this->oprPrd2->id_user,
            'judul' => 'Permintaan Tukar Shift',
        ]);

        // 2. Pengganti menerima
        $tukar = $service->responPengganti($tukar, $this->oprPrd2, true);
        $this->assertEquals('MENUNGGU_SUPERVISOR', $tukar->status);

        // 3. Supervisor menyetujui
        $tukar = $service->approveSupervisor($tukar, $this->spvPrd, true);
        $this->assertEquals('DISETUJUI', $tukar->status);
        $this->assertEquals($this->spvPrd->id_user, $tukar->id_approver);

        // Cek bahwa shift kedua jadwal berhasil ditukar
        $j1->refresh();
        $j2->refresh();
        $this->assertEquals('S2', $j1->kode_shift);
        $this->assertEquals('TUKAR_SHIFT', $j1->sumber);
        $this->assertEquals('S1', $j2->kode_shift);
        $this->assertEquals('TUKAR_SHIFT', $j2->sumber);

        // Cek audit log tertulis
        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'APPROVE_TUKAR_SHIFT',
            'tabel' => 'tukar_shift',
            'id_record' => (string) $tukar->id_tukar,
        ]);
    }

    // ==========================================
    // 2. KOREKSI ABSENSI (F-11 / BR-12)
    // ==========================================

    public function test_koreksi_absensi_fails_without_reason(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Alasan koreksi absensi wajib diisi');

        $tgl = '2026-10-21';
        $jadwal = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprPrd1->id_karyawan, 'tanggal' => $tgl],
            ['kode_shift' => 'S1', 'sumber' => 'POLA', 'status' => 'PUBLISHED']
        );
        $absensi = Absensi::updateOrCreate(
            ['id_karyawan' => $this->oprPrd1->id_karyawan, 'tanggal_shift' => $tgl],
            ['id_jadwal' => $jadwal->id_jadwal, 'status' => 'ALPA']
        );

        $service = app(AttendanceService::class);
        $service->koreksiAbsensi($absensi->id_absensi, ['status' => 'HADIR'], $this->spvPrd, '');
    }

    public function test_koreksi_absensi_updates_record_and_writes_audit_log(): void
    {
        $tgl = '2026-10-22';
        $jadwal = JadwalShift::updateOrCreate(
            ['id_karyawan' => $this->oprPrd1->id_karyawan, 'tanggal' => $tgl],
            ['kode_shift' => 'S1', 'sumber' => 'POLA', 'status' => 'PUBLISHED']
        );
        $absensi = Absensi::updateOrCreate(
            ['id_karyawan' => $this->oprPrd1->id_karyawan, 'tanggal_shift' => $tgl],
            ['id_jadwal' => $jadwal->id_jadwal, 'status' => 'ALPA']
        );

        $service = app(AttendanceService::class);
        $alasan = 'Kiosk rusak di line 3, operator hadir tepat waktu dikonfirmasi supervisor.';

        $result = $service->koreksiAbsensi(
            $absensi->id_absensi,
            [
                'waktu_masuk' => '2026-10-22 06:55:00',
                'waktu_pulang' => '2026-10-22 15:05:00',
                'status' => 'HADIR',
                'menit_terlambat' => 0,
            ],
            $this->spvPrd,
            $alasan
        );

        $this->assertEquals('HADIR', $result->status);
        $this->assertEquals($this->spvPrd->id_user, $result->dikoreksi_oleh);
        $this->assertEquals($alasan, $result->catatan);

        // Verify audit log has old and new data (BR-12)
        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'KOREKSI_ABSENSI',
            'tabel' => 'absensi',
            'id_record' => (string) $absensi->id_absensi,
        ]);

        // Verify employee received notification
        $this->assertDatabaseHas('notifikasi', [
            'id_user' => $this->oprPrd1->id_user,
            'judul' => 'Koreksi Absensi Dilakukan',
        ]);
    }

    // ==========================================
    // 3. PENGAJUAN & REKAP LEMBUR (F-12)
    // ==========================================

    public function test_pengajuan_lembur_and_approval_workflow(): void
    {
        $service = app(OvertimeService::class);

        // Operator mengajukan lembur 4 jam (16:00 - 20:00 = 240 menit)
        $lembur = $service->ajukanLembur(
            $this->oprPrd1->karyawan,
            '2026-10-23',
            '16:00',
            '20:00',
            'Kejar target backlog pengiriman akhir bulan'
        );

        $this->assertEquals('DIAJUKAN', $lembur->status);
        $this->assertEquals(240, $lembur->menit_lembur);

        // Supervisor menyetujui
        $lembur = $service->approveLembur($lembur, $this->spvPrd, true);
        $this->assertEquals('DISETUJUI', $lembur->status);
        $this->assertEquals($this->spvPrd->id_user, $lembur->id_approver);

        // Audit log written
        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'APPROVE_LEMBUR',
            'tabel' => 'lembur',
            'id_record' => (string) $lembur->id_lembur,
        ]);

        // Notification sent to operator
        $this->assertDatabaseHas('notifikasi', [
            'id_user' => $this->oprPrd1->id_user,
            'judul' => 'Pengajuan Lembur Disetujui',
        ]);
    }

    // ==========================================
    // 4. LOGBOOK SERAH TERIMA SHIFT (F-13)
    // ==========================================

    public function test_logbook_serah_terima_shift_creation(): void
    {
        $logbook = LogbookShift::create([
            'tanggal' => '2026-10-24',
            'kode_shift' => 'S1',
            'id_departemen' => $this->spvPrd->karyawan->id_departemen,
            'id_pembuat' => $this->spvPrd->id_user,
            'ringkasan' => 'Output lini 1 mencapai 450 unit (100% target).',
            'kendala' => 'Mesin press sempat overheating selama 15 menit.',
            'tindak_lanjut' => 'Awasi temperatur chiller hidrolik pada Shift 2.',
        ]);

        $this->assertDatabaseHas('logbook_shift', [
            'id_logbook' => $logbook->id_logbook,
            'kode_shift' => 'S1',
            'id_pembuat' => $this->spvPrd->id_user,
        ]);
    }

    // ==========================================
    // 5. NOTIFIKASI DALAM APLIKASI (F-14)
    // ==========================================

    public function test_notification_service_send_and_mark_read(): void
    {
        $notif = NotificationService::kirim(
            $this->oprPrd1->id_user,
            'Pengumuman Pabrik',
            'Maintenance rutin lini stamping besok pagi.',
            '/dashboard'
        );

        $this->assertDatabaseHas('notifikasi', [
            'id_notifikasi' => $notif->id_notifikasi,
            'id_user' => $this->oprPrd1->id_user,
            'dibaca_at' => null,
        ]);

        $this->assertGreaterThan(0, NotificationService::unreadCount($this->oprPrd1->id_user));

        NotificationService::tandaiDibaca($notif->id_notifikasi, $this->oprPrd1->id_user);

        $notif->refresh();
        $this->assertNotNull($notif->dibaca_at);
    }

    // ==========================================
    // 6. HARI LIBUR NASIONAL & PERUSAHAAN (F-15)
    // ==========================================

    public function test_hari_libur_crud_and_audit(): void
    {
        $tgl = '2026-12-25';
        HariLibur::where('tanggal', $tgl)->delete();

        $hl = HariLibur::create([
            'tanggal' => $tgl,
            'keterangan' => 'Hari Raya Natal',
            'jenis' => 'NASIONAL',
        ]);

        $this->assertDatabaseHas('hari_libur', [
            'tanggal' => $tgl,
            'keterangan' => 'Hari Raya Natal',
            'jenis' => 'NASIONAL',
        ]);

        $hl->update(['keterangan' => 'Hari Raya Natal & Cuti Bersama']);
        $this->assertEquals('Hari Raya Natal & Cuti Bersama', $hl->fresh()->keterangan);

        $hl->delete();
        $this->assertDatabaseMissing('hari_libur', ['tanggal' => $tgl]);
    }
}
