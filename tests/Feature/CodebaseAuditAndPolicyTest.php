<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\GrupKaryawan;
use App\Models\Shift;
use App\Models\PolaRotasi;
use App\Models\Pengajuan;
use App\Models\HariLibur;
use App\Models\Pengaturan;
use App\Services\PengajuanService;
use Livewire\Livewire;
use App\Livewire\MasterKaryawan;
use App\Livewire\PolaRotasiManager;
use App\Livewire\HariLiburManager;
use InvalidArgumentException;

class CodebaseAuditAndPolicyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_login_request_validates_required_fields(): void
    {
        $response = $this->post('/login', [
            'id_karyawan' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['id_karyawan', 'password']);
    }

    public function test_dashboard_renders_without_direct_blade_model_queries(): void
    {
        $user = User::where('role', 'ADMIN_HR')->first();
        $this->actingAs($user);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas(['totalKaryawan', 'totalDepartemen', 'totalGrup', 'totalShift', 'shifts']);
    }

    public function test_operator_forbidden_from_master_karyawan(): void
    {
        $operatorUser = User::where('role', 'OPERATOR')->first();

        Livewire::actingAs($operatorUser)
            ->test(MasterKaryawan::class)
            ->assertForbidden();
    }

    public function test_operator_forbidden_from_pola_rotasi(): void
    {
        $operatorUser = User::where('role', 'OPERATOR')->first();

        Livewire::actingAs($operatorUser)
            ->test(PolaRotasiManager::class)
            ->assertForbidden();
    }

    public function test_supervisor_cannot_generate_schedule_for_other_department(): void
    {
        $supervisor = User::where('role', 'SUPERVISOR')->first();
        $otherDept = Departemen::where('id_departemen', '!=', $supervisor->karyawan->id_departemen)->first();
        $pola = PolaRotasi::first();

        Livewire::actingAs($supervisor)
            ->test(PolaRotasiManager::class)
            ->set('targetDepartemenId', $otherDept->id_departemen)
            ->set('targetBulan', now()->format('Y-m'))
            ->set('tanggalMulaiSiklus', now()->startOfMonth()->format('Y-m-d'))
            ->set('selectedPolaId', $pola->id_pola)
            ->call('generateJadwal')
            ->assertForbidden();
    }

    public function test_operator_cannot_mutate_hari_libur(): void
    {
        $operatorUser = User::where('role', 'OPERATOR')->first();

        $this->actingAs($operatorUser)
            ->get('/hari-libur')
            ->assertForbidden();
    }

    public function test_pengajuan_service_escalates_to_hod_when_cuti_exceeds_threshold(): void
    {
        Pengaturan::setValue('cuti_perlu_hod_hari', '3');

        $operator = Karyawan::whereHas('user', fn($q) => $q->where('role', 'OPERATOR'))->first();
        $supervisor = User::where('role', 'SUPERVISOR')
            ->whereHas('karyawan', fn($q) => $q->where('id_departemen', $operator->id_departemen))
            ->first();

        $service = app(PengajuanService::class);

        // Ajukan cuti 5 hari (> 3 hari)
        $pengajuan = $service->ajukan(
            $operator,
            'CUTI',
            '2026-11-01',
            '2026-11-05',
            'Cuti tahunan 5 hari'
        );

        $this->assertEquals('DIAJUKAN', $pengajuan->status);
        $this->assertEquals('SUPERVISOR', $pengajuan->tahap);

        // Supervisor menyetujui -> harus naik ke HOD (Rule 12)
        $result = $service->approve($pengajuan, $supervisor, true);

        $this->assertEquals('DIAJUKAN', $result->status);
        $this->assertEquals('HOD', $result->tahap);
        $this->assertStringContainsString('Diteruskan ke HOD', $result->catatan_approver);
    }

    public function test_pengajuan_service_approves_directly_when_duration_within_threshold(): void
    {
        Pengaturan::setValue('cuti_perlu_hod_hari', '3');

        $operator = Karyawan::whereHas('user', fn($q) => $q->where('role', 'OPERATOR'))->first();
        $supervisor = User::where('role', 'SUPERVISOR')
            ->whereHas('karyawan', fn($q) => $q->where('id_departemen', $operator->id_departemen))
            ->first();

        $service = app(PengajuanService::class);

        // Ajukan cuti 2 hari (<= 3 hari)
        $pengajuan = $service->ajukan(
            $operator,
            'CUTI',
            '2026-11-01',
            '2026-11-02',
            'Cuti singkat 2 hari'
        );

        // Supervisor menyetujui -> langsung final DISETUJUI
        $result = $service->approve($pengajuan, $supervisor, true);

        $this->assertEquals('DISETUJUI', $result->status);
        $this->assertEquals('SELESAI', $result->tahap);
    }

    public function test_pengajuan_cross_department_approver_rejected(): void
    {
        $operator = Karyawan::whereHas('user', fn($q) => $q->where('role', 'OPERATOR'))->first();
        $otherSupervisor = User::where('role', 'SUPERVISOR')
            ->whereHas('karyawan', fn($q) => $q->where('id_departemen', '!=', $operator->id_departemen))
            ->first();

        $service = app(PengajuanService::class);
        $pengajuan = $service->ajukan($operator, 'IZIN', '2026-11-01', '2026-11-01', 'Urusan keluarga');

        $this->expectException(InvalidArgumentException::class);
        $service->approve($pengajuan, $otherSupervisor, true);
    }

    public function test_pengajuan_service_rejection_marks_status_ditolak(): void
    {
        $operator = Karyawan::whereHas('user', fn($q) => $q->where('role', 'OPERATOR'))->first();
        $supervisor = User::where('role', 'SUPERVISOR')
            ->whereHas('karyawan', fn($q) => $q->where('id_departemen', $operator->id_departemen))
            ->first();

        $service = app(PengajuanService::class);
        $pengajuan = $service->ajukan($operator, 'IZIN', '2026-11-01', '2026-11-01', 'Keperluan mendesak');

        $result = $service->approve($pengajuan, $supervisor, false, 'Staf shift kurang');

        $this->assertEquals('DITOLAK', $result->status);
        $this->assertEquals('SELESAI', $result->tahap);
        $this->assertEquals('Staf shift kurang', $result->catatan_approver);
    }
}
