<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Pengajuan;
use App\Models\TukarShift;
use App\Models\Lembur;
use App\Models\LogbookShift;
use App\Models\HariLibur;
use App\Models\Pengaturan;
use App\Models\AuditLog;
use App\Models\Departemen;
use App\Models\JadwalShift;
use App\Livewire\PengajuanApprovalHub;
use App\Livewire\MasterKaryawan;
use App\Livewire\KalenderJadwal;
use App\Livewire\RekapBulanan;
use App\Livewire\PengaturanManager;
use App\Livewire\AuditLogIndex;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

class RoleMenuAccessAndApprovalTest extends TestCase
{
    protected User $adminHr;
    protected User $direktur;
    protected User $hodPrd;
    protected User $spvPrd;
    protected User $oprPrd;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminHr = User::where('id_karyawan', 'EMP-0001')->firstOrFail();
        $this->direktur = User::where('id_karyawan', 'EMP-0002')->firstOrFail();
        $this->hodPrd = User::where('id_karyawan', 'EMP-0004')->firstOrFail();
        $this->spvPrd = User::where('id_karyawan', 'EMP-0009')->firstOrFail();
        $this->oprPrd = User::where('id_karyawan', 'EMP-0013')->firstOrFail();
    }

    /**
     * Poin 1: Admin HR dapat membuka semua 11 menu sidebar dengan HTTP 200
     * dan menampilkan data semua departemen.
     */
    public function test_admin_hr_can_access_all_11_sidebar_menus(): void
    {
        $menus = [
            '/dashboard',
            '/jadwal',
            '/absensi/koreksi',
            '/pengajuan-approval',
            '/logbook',
            '/rekap',
            '/master/karyawan',
            '/pola-rotasi',
            '/hari-libur',
            '/pengaturan',
            '/audit-log',
        ];

        foreach ($menus as $url) {
            $response = $this->actingAs($this->adminHr)->get($url);
            $response->assertStatus(200, "Admin HR harus dapat membuka menu {$url} tanpa 403/404/500");
        }
    }

    /**
     * Poin 2: Pembatasan menu khusus Admin HR (Pengaturan Sistem, Audit Log, Hari Libur).
     * Role lain (Direktur, HOD, Supervisor, Operator) harus mendapat 403 Forbidden.
     */
    public function test_non_admin_hr_roles_cannot_access_restricted_menus(): void
    {
        $nonAdminRoles = [
            'Direktur' => $this->direktur,
            'HOD' => $this->hodPrd,
            'Supervisor' => $this->spvPrd,
            'Operator' => $this->oprPrd,
        ];

        $restrictedUrls = [
            '/pengaturan',
            '/audit-log',
            '/hari-libur',
        ];

        foreach ($nonAdminRoles as $roleName => $user) {
            foreach ($restrictedUrls as $url) {
                $response = $this->actingAs($user)->get($url);
                $response->assertStatus(403, "Role {$roleName} harus mendapat 403 saat mengakses {$url}");
            }
        }
    }

    /**
     * Poin 2: Operator dibatasi sesuai docs/UI_UX.md bagian 6.2 (tidak boleh mengakses rekap, master, logbook, pola rotasi).
     */
    public function test_operator_cannot_access_rekap_and_restricted_pages(): void
    {
        $this->actingAs($this->oprPrd)->get('/rekap')->assertStatus(403);
        $this->actingAs($this->oprPrd)->get('/master/karyawan')->assertStatus(403);
        $this->actingAs($this->oprPrd)->get('/logbook')->assertStatus(403);
        $this->actingAs($this->oprPrd)->get('/pola-rotasi')->assertStatus(403);
    }

    /**
     * Poin 1 & 2: Matriks otorisasi sitemap UI_UX.md 6.2 untuk kelima role.
     */
    public function test_sitemap_matrix_for_all_five_roles(): void
    {
        // 1. Operator: Beranda, Jadwal Saya, Riwayat Absensi, Pengajuan
        $this->actingAs($this->oprPrd)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->oprPrd)->get('/jadwal')->assertStatus(200);
        $this->actingAs($this->oprPrd)->get('/absensi/koreksi')->assertStatus(200);
        $this->actingAs($this->oprPrd)->get('/pengajuan-approval')->assertStatus(200);

        // 2. Supervisor: Dashboard, Jadwal, Absensi Tim, Approval, Logbook, Rekap
        $this->actingAs($this->spvPrd)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->spvPrd)->get('/jadwal')->assertStatus(200);
        $this->actingAs($this->spvPrd)->get('/absensi/koreksi')->assertStatus(200);
        $this->actingAs($this->spvPrd)->get('/pengajuan-approval')->assertStatus(200);
        $this->actingAs($this->spvPrd)->get('/logbook')->assertStatus(200);
        $this->actingAs($this->spvPrd)->get('/rekap')->assertStatus(200);

        // 3. HOD: Dashboard, Jadwal, Approval, Rekap
        $this->actingAs($this->hodPrd)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->hodPrd)->get('/jadwal')->assertStatus(200);
        $this->actingAs($this->hodPrd)->get('/pengajuan-approval')->assertStatus(200);
        $this->actingAs($this->hodPrd)->get('/rekap')->assertStatus(200);

        // 4. Direktur: Dashboard, Rekap
        $this->actingAs($this->direktur)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->direktur)->get('/rekap')->assertStatus(200);

        // 5. Admin HR: Seluruh 11 Menu
        foreach (['/dashboard', '/jadwal', '/absensi/koreksi', '/pengajuan-approval', '/logbook', '/rekap', '/master/karyawan', '/pola-rotasi', '/hari-libur', '/pengaturan', '/audit-log'] as $url) {
            $this->actingAs($this->adminHr)->get($url)->assertStatus(200);
        }
    }

    /**
     * Poin 2: Sidebar menu Hari Libur, Pengaturan Sistem, dan Audit Log tidak tampil untuk non-Admin HR.
     */
    public function test_restricted_sidebar_menus_not_visible_for_non_admin_hr(): void
    {
        $response = $this->actingAs($this->spvPrd)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee(route('hari.libur'));
        $response->assertDontSee(route('pengaturan.sistem'));
        $response->assertDontSee(route('audit.log'));
    }

    /**
     * Poin 3: Antrean Approval untuk Admin HR.
     * Admin HR boleh membuka Antrean Approval dan melihat semua pengajuan di semua departemen,
     * tetapi tombol Setuju dan Tolak tidak ditampilkan, dan Policy menolak approval dari Admin HR.
     */
    public function test_admin_hr_can_view_all_approvals_but_cannot_approve(): void
    {
        // Pastikan ada pengajuan dari operator Produksi untuk dites
        $pengajuan = Pengajuan::where('id_karyawan', $this->oprPrd->id_karyawan)
            ->where('status', 'DIAJUKAN')
            ->where('tahap', 'SUPERVISOR')
            ->first();

        if (!$pengajuan) {
            $pengajuan = Pengajuan::create([
                'id_karyawan' => $this->oprPrd->id_karyawan,
                'jenis' => 'CUTI_TAHUNAN',
                'tgl_mulai' => now('Asia/Jakarta')->addDays(2)->toDateString(),
                'tgl_selesai' => now('Asia/Jakarta')->addDays(3)->toDateString(),
                'alasan' => 'Keperluan keluarga mendesak',
                'status' => 'DIAJUKAN',
                'tahap' => 'SUPERVISOR',
            ]);
        }

        // 1. Admin HR buka halaman Antrean Approval -> 200 OK
        $response = $this->actingAs($this->adminHr)->get('/pengajuan-approval');
        $response->assertStatus(200);

        // 2. Gate policy check: Admin HR DITOLAK menyetujui pengajuan
        $this->assertFalse(
            Gate::forUser($this->adminHr)->allows('approve', $pengajuan),
            'Admin HR tidak boleh memiliki izin approve pengajuan cuti/izin'
        );

        // 3. Supervisor berhak approve pengajuan departemennya
        $this->assertTrue(
            Gate::forUser($this->spvPrd)->allows('approve', $pengajuan),
            'Supervisor harus memiliki izin approve pengajuan timnya'
        );

        // 4. Livewire render check: pastikan tombol Setuju tidak muncul ketika Admin HR melihat detail pengajuan
        Livewire::actingAs($this->adminHr)
            ->test(PengajuanApprovalHub::class)
            ->call('selectItem', 'PENGAJUAN', $pengajuan->id_pengajuan)
            ->assertDontSee('approvePengajuan(' . $pengajuan->id_pengajuan . ', true)')
            ->assertDontSee('approvePengajuan(' . $pengajuan->id_pengajuan . ', false)');
    }

    /**
     * Poin 3: Admin HR ditolak menyetujui Tukar Shift dan Lembur.
     */
    public function test_admin_hr_cannot_approve_tukar_shift_and_lembur(): void
    {
        $tukar = TukarShift::first();
        if ($tukar) {
            $this->assertFalse(
                Gate::forUser($this->adminHr)->allows('approveSupervisor', $tukar),
                'Admin HR tidak boleh memiliki izin approveSupervisor pada tukar shift'
            );
        }

        $lembur = Lembur::first();
        if ($lembur) {
            $this->assertFalse(
                Gate::forUser($this->adminHr)->allows('approve', $lembur),
                'Admin HR tidak boleh memiliki izin approve pada lembur'
            );
        }
    }

    /**
     * Poin 3: Logbook Shift: Admin HR boleh melihat, tetapi yang mengisi HANYA Supervisor.
     */
    public function test_only_supervisor_can_create_logbook_shift(): void
    {
        // Admin HR boleh view logbook
        $this->actingAs($this->adminHr)->get('/logbook')->assertStatus(200);

        // Policy create check
        $this->assertFalse(
            Gate::forUser($this->adminHr)->allows('create', LogbookShift::class),
            'Admin HR tidak boleh membuat logbook shift'
        );

        $this->assertFalse(
            Gate::forUser($this->oprPrd)->allows('create', LogbookShift::class),
            'Operator tidak boleh membuat logbook shift'
        );

        $this->assertTrue(
            Gate::forUser($this->spvPrd)->allows('create', LogbookShift::class),
            'Supervisor harus diizinkan membuat logbook shift'
        );
    }

    /**
     * Poin 4: Admin HR dapat mengelola akun pengguna, mengatur role, reset password,
     * dan mengatur serta mereset PIN absen 6 digit yang tersimpan sebagai hash.
     */
    public function test_admin_hr_can_manage_user_account_and_hashed_pin(): void
    {
        $targetKaryawan = Karyawan::where('id_karyawan', 'EMP-0014')->firstOrFail();

        // 1. Admin HR mengatur role baru, password baru, dan PIN 6-digit baru
        Livewire::actingAs($this->adminHr)
            ->test(MasterKaryawan::class)
            ->call('openAccountModal', $targetKaryawan->id_karyawan)
            ->set('accountRole', 'OPERATOR')
            ->set('accountActive', true)
            ->set('newPassword', 'rahasia123')
            ->set('newPin', '654321')
            ->call('simpanAkun')
            ->assertHasNoErrors();

        $user = User::where('id_karyawan', $targetKaryawan->id_karyawan)->firstOrFail();
        $this->assertEquals('OPERATOR', $user->role);
        $this->assertTrue($user->is_active);

        // Verifikasi password & PIN disimpan sebagai bcrypt hash (tidak pernah plaintext)
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertTrue(Hash::check('654321', $user->pin_absen));
        $this->assertNotEquals('654321', $user->pin_absen, 'PIN tidak boleh tersimpan dalam bentuk plain text');

        // 2. Admin HR dapat mereset PIN ke default 6-digit (123456)
        Livewire::actingAs($this->adminHr)
            ->test(MasterKaryawan::class)
            ->call('openAccountModal', $targetKaryawan->id_karyawan)
            ->call('resetPinDefault')
            ->assertHasNoErrors();

        $user->refresh();
        $this->assertTrue(Hash::check('123456', $user->pin_absen));

        // 3. Verifikasi AuditLog mencatat aktivitas mutasi akun dan PIN
        $auditLog = AuditLog::where('tabel', 'users')
            ->where('id_record', (string) $user->id_user)
            ->latest('id_log')
            ->first();

        $this->assertNotNull($auditLog, 'Perubahan akun dan PIN wajib tercatat di audit_log');
    }

    /**
     * Poin 1 & 5: Pengaturan Sistem dapat diubah oleh Admin HR dan tercatat di audit log.
     */
    public function test_admin_hr_can_update_system_settings_with_audit_log(): void
    {
        Livewire::actingAs($this->adminHr)
            ->test(PengaturanManager::class)
            ->call('editSetting', 'toleransi_terlambat_menit')
            ->set('selectedValue', '15')
            ->call('simpanSetting')
            ->assertHasNoErrors();

        $this->assertEquals('15', Pengaturan::getValue('toleransi_terlambat_menit'));

        $log = AuditLog::where('tabel', 'pengaturan')
            ->where('id_record', 'toleransi_terlambat_menit')
            ->latest('id_log')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('UPDATE_PENGATURAN', $log->aksi);

        // Kembalikan ke 10
        Pengaturan::setValue('toleransi_terlambat_menit', '10');
    }
}
