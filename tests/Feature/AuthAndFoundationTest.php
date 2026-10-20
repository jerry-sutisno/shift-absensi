<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Shift;
use App\Models\GrupKaryawan;
use App\Models\JadwalShift;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Gate;

class AuthAndFoundationTest extends TestCase
{
    public function test_guest_is_redirected_to_login_page(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $responseRoot = $this->get('/');
        $responseRoot->assertRedirect('/login');
    }

    public function test_login_screen_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Sistem');
        $response->assertSee('PlantShift OS');
        $response->assertSee('ID Karyawan / NIK');
    }

    public function test_failed_login_with_wrong_credentials(): void
    {
        $response = $this->post('/login', [
            'id_karyawan' => 'EMP-0001',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('id_karyawan');
        $this->assertGuest();
    }

    public function test_successful_login_and_audit_log_written(): void
    {
        $user = User::where('id_karyawan', 'EMP-0001')->first();
        $this->assertNotNull($user, 'User EMP-0001 must exist from seeder');

        $response = $this->post('/login', [
            'id_karyawan' => 'EMP-0001',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        // Verify audit log
        $this->assertDatabaseHas('audit_log', [
            'id_user' => $user->id_user,
            'aksi' => 'LOGIN',
            'tabel' => 'users',
        ]);
    }

    public function test_authenticated_user_can_access_dashboard_with_layout(): void
    {
        $user = User::where('id_karyawan', 'EMP-0001')->first();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Selamat Datang, ' . $user->nama);
        $response->assertSee('ADMIN_HR');
        $response->assertSee('Total Karyawan');
        $response->assertSee('PlantShift OS');
    }

    public function test_user_can_logout_successfully(): void
    {
        $user = User::where('id_karyawan', 'EMP-0001')->first();

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();

        $this->assertDatabaseHas('audit_log', [
            'id_user' => $user->id_user,
            'aksi' => 'LOGOUT',
            'tabel' => 'users',
        ]);
    }

    public function test_seeded_master_data_integrity(): void
    {
        // 5 Departemen
        $this->assertEquals(5, Departemen::count());
        $this->assertDatabaseHas('departemen', ['kode_departemen' => 'PRS']);
        $this->assertDatabaseHas('departemen', ['kode_departemen' => 'PRD']);
        $this->assertDatabaseHas('departemen', ['kode_departemen' => 'LGS']);
        $this->assertDatabaseHas('departemen', ['kode_departemen' => 'QCT']);
        $this->assertDatabaseHas('departemen', ['kode_departemen' => 'PCKG']);

        // 5 Shift
        $this->assertEquals(5, Shift::count());
        $this->assertDatabaseHas('shift', ['kode_shift' => 'GS']);
        $this->assertDatabaseHas('shift', ['kode_shift' => 'S1']);
        $this->assertDatabaseHas('shift', ['kode_shift' => 'S2']);
        $this->assertDatabaseHas('shift', ['kode_shift' => 'S3', 'lintas_hari' => 1]);
        $this->assertDatabaseHas('shift', ['kode_shift' => 'OFF', 'is_libur' => 1]);

        // 4 Grup
        $this->assertEquals(4, GrupKaryawan::count());

        // Minimal 30 Karyawan & Akun
        $this->assertGreaterThanOrEqual(30, Karyawan::count());
        $this->assertGreaterThanOrEqual(30, User::count());
    }

    public function test_policy_enforces_department_isolation_and_draft_protection(): void
    {
        $admin = User::where('id_karyawan', 'EMP-0001')->first(); // PRS
        $spvPrd = User::where('id_karyawan', 'EMP-0009')->first(); // PRD
        $oprPrd = User::where('id_karyawan', 'EMP-0013')->first(); // PRD Operator
        $oprLgs = User::where('id_karyawan', 'EMP-0025')->first(); // LGS Operator

        // Create dummy draft and published schedules
        $draftSchedulePrd = JadwalShift::create([
            'id_karyawan' => $oprPrd->id_karyawan,
            'tanggal' => '2026-11-01',
            'kode_shift' => 'S1',
            'sumber' => 'MANUAL',
            'status' => 'DRAFT',
        ]);

        $publishedSchedulePrd = JadwalShift::create([
            'id_karyawan' => $oprPrd->id_karyawan,
            'tanggal' => '2026-11-02',
            'kode_shift' => 'S2',
            'sumber' => 'MANUAL',
            'status' => 'PUBLISHED',
        ]);

        $publishedScheduleLgs = JadwalShift::create([
            'id_karyawan' => $oprLgs->id_karyawan,
            'tanggal' => '2026-11-01',
            'kode_shift' => 'GS',
            'sumber' => 'MANUAL',
            'status' => 'PUBLISHED',
        ]);

        // 1. Admin HR can view all
        $this->assertTrue(Gate::forUser($admin)->allows('view', $draftSchedulePrd));
        $this->assertTrue(Gate::forUser($admin)->allows('view', $publishedScheduleLgs));

        // 2. Supervisor PRD can view own department, but cannot view LGS
        $this->assertTrue(Gate::forUser($spvPrd)->allows('view', $draftSchedulePrd));
        $this->assertTrue(Gate::forUser($spvPrd)->allows('view', $publishedSchedulePrd));
        $this->assertFalse(Gate::forUser($spvPrd)->allows('view', $publishedScheduleLgs));

        // 3. Operator PRD can view own PUBLISHED schedule, but CANNOT view DRAFT schedule
        $this->assertTrue(Gate::forUser($oprPrd)->allows('view', $publishedSchedulePrd));
        $this->assertFalse(Gate::forUser($oprPrd)->allows('view', $draftSchedulePrd));
        $this->assertFalse(Gate::forUser($oprPrd)->allows('view', $publishedScheduleLgs));

        // Clean up created test schedules
        $draftSchedulePrd->delete();
        $publishedSchedulePrd->delete();
        $publishedScheduleLgs->delete();
    }
}
