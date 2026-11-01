<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\GrupKaryawan;
use App\Models\PolaRotasi;
use App\Models\PolaRotasiDetail;
use App\Models\JadwalShift;
use App\Models\AuditLog;
use App\Livewire\MasterKaryawan;
use App\Livewire\PolaRotasiManager;
use App\Services\ScheduleGeneratorService;
use Livewire\Livewire;
use Carbon\Carbon;

class MasterDataAndRotationTest extends TestCase
{
    protected User $adminHr;
    protected User $operator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminHr = User::where('id_karyawan', 'EMP-0001')->firstOrFail();
        $this->operator = User::where('role', 'OPERATOR')->firstOrFail();
    }

    public function test_guest_cannot_access_master_karyawan(): void
    {
        $response = $this->get('/master/karyawan');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_master_karyawan_page(): void
    {
        $response = $this->actingAs($this->adminHr)->get('/master/karyawan');
        $response->assertStatus(200);
        $response->assertSee('Master Data Karyawan Pabrik');
        $response->assertSee('Total Karyawan');
        $response->assertSee('+ Tambah Karyawan');
    }

    public function test_livewire_master_karyawan_search_filter(): void
    {
        Livewire::actingAs($this->adminHr)
            ->test(MasterKaryawan::class)
            ->set('search', 'Gilang')
            ->assertSee('Gilang')
            ->set('search', 'NONEXISTENT_NAME_XYZ')
            ->assertSee('Tidak ada karyawan yang cocok');
    }

    public function test_livewire_master_karyawan_create_employee_and_write_audit_log(): void
    {
        $dept = Departemen::firstOrFail();
        $jabatan = Jabatan::where('id_departemen', $dept->id_departemen)->firstOrFail();
        $newId = 'OP-999999';

        // Ensure clean test state
        Karyawan::where('id_karyawan', $newId)->delete();
        User::where('id_karyawan', $newId)->delete();

        Livewire::actingAs($this->adminHr)
            ->test(MasterKaryawan::class)
            ->call('openCreateDrawer')
            ->set('id_karyawan', $newId)
            ->set('nama_karyawan', 'Test Operator Baru')
            ->set('jns_kelamin', 'L')
            ->set('tgl_lahir', '1995-05-15')
            ->set('no_hp', '081234567899')
            ->set('id_departemen', $dept->id_departemen)
            ->set('id_jabatan', $jabatan->id_jabatan)
            ->set('id_grup', null)
            ->set('role', 'OPERATOR')
            ->set('create_user_account', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('karyawan', [
            'id_karyawan' => $newId,
            'nama_karyawan' => 'Test Operator Baru',
            'status_karyawan' => 'AKTIF',
        ]);

        $this->assertDatabaseHas('users', [
            'id_karyawan' => $newId,
            'role' => 'OPERATOR',
        ]);

        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'CREATE',
            'tabel' => 'karyawan',
            'id_record' => $newId,
        ]);
    }

    public function test_livewire_master_karyawan_toggle_status_and_audit(): void
    {
        $karyawan = Karyawan::whereNotIn('id_karyawan', ['EMP-0001', 'EMP-0002', 'EMP-0004', 'EMP-0009', 'EMP-0013'])->firstOrFail();
        $initialStatus = $karyawan->status_karyawan;

        Livewire::actingAs($this->adminHr)
            ->test(MasterKaryawan::class)
            ->call('toggleStatus', $karyawan->id_karyawan);

        $karyawan->refresh();
        $expectedStatus = $initialStatus === 'AKTIF' ? 'NONAKTIF' : 'AKTIF';
        $this->assertEquals($expectedStatus, $karyawan->status_karyawan);

        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'TOGGLE_STATUS',
            'tabel' => 'karyawan',
            'id_record' => $karyawan->id_karyawan,
        ]);

        // Kembalikan ke status awal agar tidak mempengaruhi data live demo
        Livewire::actingAs($this->adminHr)
            ->test(MasterKaryawan::class)
            ->call('toggleStatus', $karyawan->id_karyawan);
        $karyawan->refresh();
        $this->assertEquals($initialStatus, $karyawan->status_karyawan);
    }

    public function test_pola_rotasi_screen_renders_successfully(): void
    {
        $response = $this->actingAs($this->adminHr)->get('/pola-rotasi');
        $response->assertStatus(200);
        $response->assertSee('Pola Rotasi');
        $response->assertSee('Parameter Generator Jadwal');
        $response->assertSee('Validasi Manpower');
    }

    public function test_livewire_pola_rotasi_can_cycle_shift(): void
    {
        $grup = GrupKaryawan::firstOrFail();
        $pola = PolaRotasi::firstOrFail();

        $currentDetail = PolaRotasiDetail::where('id_pola', $pola->id_pola)
            ->where('id_grup', $grup->id_grup)
            ->where('hari_ke', 1)
            ->firstOrFail();

        $initialShift = $currentDetail->kode_shift;

        Livewire::actingAs($this->adminHr)
            ->test(PolaRotasiManager::class)
            ->call('cycleShiftCell', $grup->id_grup, 1);

        $currentDetail->refresh();
        $this->assertNotEquals($initialShift, $currentDetail->kode_shift);

        // Revert to maintain database consistency
        $currentDetail->update(['kode_shift' => $initialShift]);
    }

    public function test_schedule_generator_service_creates_draft_and_respects_rules(): void
    {
        $dept = Departemen::where('kode_departemen', 'PRD')->firstOrFail();
        $pola = PolaRotasi::firstOrFail();
        $service = app(ScheduleGeneratorService::class);

        $result = $service->generateBulanan(
            $dept->id_departemen,
            2026,
            12,
            $pola->id_pola,
            '2026-12-01'
        );

        $this->assertTrue($result['status']);
        $this->assertGreaterThan(0, $result['generated']);

        // Check that created schedules have status DRAFT (Rule 8: Jadwal DRAFT tidak boleh terlihat oleh Operator)
        $sampleJadwal = JadwalShift::where('tanggal', 'like', '2026-12-%')
            ->whereHas('karyawan', function ($q) use ($dept) {
                $q->where('id_departemen', $dept->id_departemen);
            })
            ->firstOrFail();

        $this->assertEquals('DRAFT', $sampleJadwal->status);
        $this->assertEquals('POLA', $sampleJadwal->sumber);

        // Verify audit log written
        $this->assertDatabaseHas('audit_log', [
            'aksi' => 'GENERATE_JADWAL',
            'tabel' => 'jadwal_shift',
        ]);
    }
}
