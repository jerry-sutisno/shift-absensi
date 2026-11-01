<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Pengajuan;
use App\Models\Lembur;
use App\Models\TukarShift;
use App\Models\Absensi;
use App\Models\JadwalShift;
use App\Models\Pengaturan;
use App\Services\PengajuanService;
use App\Services\OvertimeService;
use App\Services\TukarShiftService;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

class SecurityHardeningTest extends TestCase
{
    protected User $adminHr;
    protected User $hodPrd;
    protected User $spvPrd;
    protected User $oprPrd;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminHr = User::where('id_karyawan', 'EMP-0001')->firstOrFail();
        $this->hodPrd = User::where('id_karyawan', 'EMP-0004')->firstOrFail();
        $this->spvPrd = User::where('id_karyawan', 'EMP-0009')->firstOrFail();
        $this->oprPrd = User::where('id_karyawan', 'EMP-0013')->firstOrFail();
    }

    /**
     * 1. Pastikan route backdoor preview-auth sudah dihapus (404).
     */
    public function test_backdoor_preview_auth_route_is_removed(): void
    {
        $response = $this->get('/preview-auth/EMP-0001/dashboard');
        $response->assertStatus(404);
    }

    /**
     * 2. Pastikan HTTP Security Headers diterapkan pada response web.
     */
    public function test_security_headers_are_present(): void
    {
        $response = $this->actingAs($this->adminHr)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * 3. Cegah Self-Approval pada permohonan Cuti / Izin / Sakit.
     */
    public function test_user_cannot_approve_their_own_pengajuan(): void
    {
        // Buat pengajuan atas nama HOD
        $pengajuan = Pengajuan::create([
            'id_karyawan' => $this->hodPrd->id_karyawan,
            'jenis' => 'CUTI',
            'tgl_mulai' => now('Asia/Jakarta')->addDays(5)->toDateString(),
            'tgl_selesai' => now('Asia/Jakarta')->addDays(6)->toDateString(),
            'alasan' => 'Keperluan keluarga penting',
            'status' => 'DIAJUKAN',
            'tahap' => 'HOD',
        ]);

        // Policy harus menolak self-approval
        $this->assertFalse(
            Gate::forUser($this->hodPrd)->allows('approve', $pengajuan),
            'HOD tidak boleh diizinkan menyetujui cuti milik dirinya sendiri'
        );

        // Service juga wajib melempar Exception jika dicoba eksekusi
        $service = app(PengajuanService::class);
        $this->expectException(InvalidArgumentException::class);
        $service->approve($pengajuan, $this->hodPrd, true, 'Setuju sendiri');
    }

    /**
     * 4. Cegah Self-Approval pada permohonan Lembur.
     */
    public function test_user_cannot_approve_their_own_lembur(): void
    {
        $lembur = Lembur::create([
            'id_karyawan' => $this->spvPrd->id_karyawan,
            'tanggal' => now('Asia/Jakarta')->toDateString(),
            'jam_mulai' => '16:00',
            'jam_selesai' => '20:00',
            'alasan' => 'Maintenance darurat mesin line 1',
            'status' => 'DIAJUKAN',
            'menit_lembur' => 240,
        ]);

        // Policy harus menolak self-approval
        $this->assertFalse(
            Gate::forUser($this->spvPrd)->allows('approve', $lembur),
            'Supervisor tidak boleh diizinkan menyetujui lembur milik dirinya sendiri'
        );

        // Service juga wajib melempar Exception
        $service = app(OvertimeService::class);
        $this->expectException(InvalidArgumentException::class);
        $service->approveLembur($lembur, $this->spvPrd, true);
    }

    /**
     * 5. Cegah Supervisor menyetujui Tukar Shift yang melibatkan dirinya sendiri.
     */
    public function test_supervisor_cannot_approve_tukar_shift_involving_themselves(): void
    {
        $jadwalSpv = JadwalShift::where('id_karyawan', $this->spvPrd->id_karyawan)->first();
        $jadwalOpr = JadwalShift::where('id_karyawan', $this->oprPrd->id_karyawan)->first();

        if (!$jadwalSpv || !$jadwalOpr) {
            $this->markTestSkipped('Jadwal untuk SPV atau OPR belum tersedia.');
        }

        $tukar = TukarShift::create([
            'id_pemohon' => $this->spvPrd->id_karyawan,
            'id_pengganti' => $this->oprPrd->id_karyawan,
            'id_jadwal_pemohon' => $jadwalSpv->id_jadwal,
            'id_jadwal_pengganti' => $jadwalOpr->id_jadwal,
            'alasan' => 'Keperluan mendadak',
            'status' => 'MENUNGGU_SUPERVISOR',
        ]);

        // Policy harus menolak persetujuan supervisor jika supervisor adalah pemohon
        $this->assertFalse(
            Gate::forUser($this->spvPrd)->allows('approveSupervisor', $tukar),
            'Supervisor tidak boleh menyetujui tukar shift yang melibatkan dirinya'
        );

        // Service juga wajib melempar Exception
        $service = app(TukarShiftService::class);
        $this->expectException(InvalidArgumentException::class);
        $service->approveSupervisor($tukar, $this->spvPrd, true);
    }

    /**
     * 6. Validasi bahwa Koreksi Absensi menolak koreksi lintas departemen di Service.
     */
    public function test_koreksi_absensi_rejects_cross_department(): void
    {
        // Cari absensi karyawan di luar departemen Produksi (misal QC atau Maintenance)
        $absensiLuarDept = Absensi::whereHas('karyawan', function ($q) {
            $q->where('id_departemen', '!=', $this->spvPrd->karyawan->id_departemen);
        })->first();

        if (!$absensiLuarDept) {
            $this->markTestSkipped('Tidak ada data absensi luar departemen.');
        }

        $service = app(AttendanceService::class);
        $this->expectException(InvalidArgumentException::class);
        $service->koreksiAbsensi(
            $absensiLuarDept->id_absensi,
            ['status' => 'HADIR'],
            $this->spvPrd,
            'Mencoba koreksi luar departemen'
        );
    }
}
