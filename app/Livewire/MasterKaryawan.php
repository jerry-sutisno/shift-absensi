<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\GrupKaryawan;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

#[Layout('layouts.app')]
#[Title('Master Data Karyawan')]
class MasterKaryawan extends Component
{
    use WithPagination;

    // Filters & Search
    public string $search = '';
    public string $filterDepartemen = '';
    public string $filterGrup = '';
    public string $filterStatus = '';

    // Modal / Drawer state
    public bool $isDrawerOpen = false;
    public bool $isEditMode = false;

    // Account & PIN Modal state
    public bool $isAccountModalOpen = false;
    public ?string $selectedKaryawanId = null;
    public ?Karyawan $selectedKaryawan = null;
    public string $accountRole = 'OPERATOR';
    public bool $accountActive = true;
    public string $newPassword = '';
    public string $newPin = '';

    // Form inputs
    public string $id_karyawan = '';
    public string $nama_karyawan = '';
    public string $jns_kelamin = 'L';
    public string $tgl_lahir = '';
    public string $no_hp = '';
    public ?int $id_departemen = null;
    public ?int $id_jabatan = null;
    public ?int $id_grup = null;
    public string $status_karyawan = 'AKTIF';
    public string $role = 'OPERATOR';
    public bool $create_user_account = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterDepartemen' => ['except' => ''],
        'filterGrup' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->authorize('viewAny', Karyawan::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDepartemen(): void
    {
        $this->resetPage();
    }

    public function updatingFilterGrup(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openCreateDrawer(): void
    {
        $this->authorize('create', Karyawan::class);
        $this->resetForm();
        $this->isEditMode = false;
        $this->isDrawerOpen = true;

        // Auto-select first departemen
        $firstDept = Departemen::first();
        if ($firstDept) {
            $this->id_departemen = $firstDept->id_departemen;
            $firstJab = Jabatan::where('id_departemen', $this->id_departemen)->first();
            $this->id_jabatan = $firstJab?->id_jabatan;
        }
    }

    public function openEditDrawer(string $id): void
    {
        $this->resetForm();
        $karyawan = Karyawan::with('user')->findOrFail($id);
        $this->authorize('update', $karyawan);

        $this->isEditMode = true;
        $this->id_karyawan = $karyawan->id_karyawan;
        $this->nama_karyawan = $karyawan->nama_karyawan;
        $this->jns_kelamin = $karyawan->jns_kelamin;
        $this->tgl_lahir = $karyawan->tgl_lahir ? $karyawan->tgl_lahir->format('Y-m-d') : '';
        $this->no_hp = $karyawan->no_hp ?? '';
        $this->id_departemen = $karyawan->id_departemen;
        $this->id_jabatan = $karyawan->id_jabatan;
        $this->id_grup = $karyawan->id_grup;
        $this->status_karyawan = $karyawan->status_karyawan;
        $this->role = $karyawan->user?->role ?? 'OPERATOR';
        $this->create_user_account = false;

        $this->isDrawerOpen = true;
    }

    public function closeDrawer(): void
    {
        $this->isDrawerOpen = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->resetValidation();
        $this->id_karyawan = '';
        $this->nama_karyawan = '';
        $this->jns_kelamin = 'L';
        $this->tgl_lahir = '';
        $this->no_hp = '';
        $this->id_departemen = null;
        $this->id_jabatan = null;
        $this->id_grup = null;
        $this->status_karyawan = 'AKTIF';
        $this->role = 'OPERATOR';
        $this->create_user_account = true;
    }

    public function updatedIdDepartemen(): void
    {
        // Validasi FR-05: Jabatan harus sesuai dengan departemen
        $firstJabatan = Jabatan::where('id_departemen', $this->id_departemen)->first();
        $this->id_jabatan = $firstJabatan?->id_jabatan;
    }

    public function save(): void
    {
        $rules = [
            'id_karyawan' => [
                'required',
                'string',
                'max:20',
                $this->isEditMode 
                    ? Rule::unique('karyawan', 'id_karyawan')->ignore($this->id_karyawan, 'id_karyawan')
                    : 'unique:karyawan,id_karyawan',
            ],
            'nama_karyawan' => ['required', 'string', 'max:50'],
            'jns_kelamin' => ['required', 'in:L,P'],
            'tgl_lahir' => ['required', 'date'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'id_departemen' => ['required', 'exists:departemen,id_departemen'],
            'id_jabatan' => [
                'required',
                Rule::exists('jabatan', 'id_jabatan')->where('id_departemen', $this->id_departemen),
            ],
            'id_grup' => ['nullable', 'exists:grup_karyawan,id_grup'],
            'status_karyawan' => ['required', 'in:AKTIF,NONAKTIF'],
            'role' => ['required', 'in:ADMIN_HR,DIREKTUR,HOD,SUPERVISOR,OPERATOR'],
        ];

        $validated = $this->validate($rules, [
            'id_karyawan.required' => 'ID Karyawan wajib diisi.',
            'id_karyawan.unique' => 'ID Karyawan sudah terdaftar.',
            'nama_karyawan.required' => 'Nama Karyawan wajib diisi.',
            'id_jabatan.exists' => 'Jabatan harus berada di bawah departemen yang dipilih.',
        ]);

        if ($this->isEditMode) {
            $karyawan = Karyawan::findOrFail($this->id_karyawan);
            $this->authorize('update', $karyawan);
            $dataLama = $karyawan->toArray();

            $karyawan->update([
                'nama_karyawan' => $this->nama_karyawan,
                'jns_kelamin' => $this->jns_kelamin,
                'tgl_lahir' => $this->tgl_lahir,
                'no_hp' => $this->no_hp,
                'id_departemen' => $this->id_departemen,
                'id_jabatan' => $this->id_jabatan,
                'id_grup' => $this->id_grup ?: null,
                'status_karyawan' => $this->status_karyawan,
            ]);

            // Update user role if user exists
            if ($karyawan->user) {
                $karyawan->user->update([
                    'role' => $this->role,
                    'is_active' => $this->status_karyawan === 'AKTIF',
                ]);
            }

            AuditLog::catat('UPDATE', 'karyawan', $karyawan->id_karyawan, $dataLama, $karyawan->fresh()->toArray());
            session()->flash('success', "Data karyawan {$karyawan->nama_karyawan} ({$karyawan->id_karyawan}) berhasil diperbarui.");
        } else {
            $this->authorize('create', Karyawan::class);

            $karyawan = Karyawan::create([
                'id_karyawan' => $this->id_karyawan,
                'nama_karyawan' => $this->nama_karyawan,
                'jns_kelamin' => $this->jns_kelamin,
                'tgl_lahir' => $this->tgl_lahir,
                'no_hp' => $this->no_hp,
                'id_departemen' => $this->id_departemen,
                'id_jabatan' => $this->id_jabatan,
                'id_grup' => $this->id_grup ?: null,
                'status_karyawan' => $this->status_karyawan,
            ]);

            if ($this->create_user_account) {
                User::create([
                    'id_karyawan' => $karyawan->id_karyawan,
                    'password' => Hash::make('password'),
                    'pin_absen' => Hash::make('123456'),
                    'role' => $this->role,
                    'is_active' => $this->status_karyawan === 'AKTIF',
                ]);
            }

            AuditLog::catat('CREATE', 'karyawan', $karyawan->id_karyawan, null, $karyawan->toArray());
            session()->flash('success', "Karyawan baru {$karyawan->nama_karyawan} berhasil didaftarkan.");
        }

        $this->closeDrawer();
    }

    public function toggleStatus(string $id): void
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);
        $this->authorize('update', $karyawan);
        $statusBaru = $karyawan->status_karyawan === 'AKTIF' ? 'NONAKTIF' : 'AKTIF';
        $dataLama = $karyawan->toArray();

        $karyawan->update(['status_karyawan' => $statusBaru]);

        if ($karyawan->user) {
            $karyawan->user->update(['is_active' => $statusBaru === 'AKTIF']);
        }

        AuditLog::catat('TOGGLE_STATUS', 'karyawan', $karyawan->id_karyawan, $dataLama, ['status_karyawan' => $statusBaru]);
        session()->flash('success', "Status karyawan {$karyawan->nama_karyawan} diubah menjadi {$statusBaru}.");
    }

    public function openAccountModal(string $idKaryawan): void
    {
        $karyawan = Karyawan::with('user')->findOrFail($idKaryawan);
        $this->authorize('update', $karyawan);

        $this->selectedKaryawanId = $idKaryawan;
        $this->selectedKaryawan = $karyawan;
        $this->newPassword = '';
        $this->newPin = '';

        if ($karyawan->user) {
            $this->accountRole = $karyawan->user->role;
            $this->accountActive = $karyawan->user->is_active;
        } else {
            $this->accountRole = 'OPERATOR';
            $this->accountActive = true;
        }

        $this->isAccountModalOpen = true;
    }

    public function closeAccountModal(): void
    {
        $this->isAccountModalOpen = false;
        $this->selectedKaryawanId = null;
        $this->selectedKaryawan = null;
        $this->reset(['newPassword', 'newPin']);
    }

    public function simpanAkun(): void
    {
        $karyawan = Karyawan::with('user')->findOrFail($this->selectedKaryawanId);
        $this->authorize('update', $karyawan);

        $this->validate([
            'accountRole' => 'required|in:ADMIN_HR,DIREKTUR,HOD,SUPERVISOR,OPERATOR',
            'newPassword' => 'nullable|min:6',
            'newPin' => 'nullable|digits:6|numeric',
        ], [
            'accountRole.required' => 'Role pengguna wajib dipilih.',
            'newPassword.min' => 'Password baru minimal 6 karakter.',
            'newPin.digits' => 'PIN absen harus terdiri dari tepat 6 digit angka.',
            'newPin.numeric' => 'PIN absen harus berupa angka.',
        ]);

        if ($karyawan->user) {
            $user = $karyawan->user;
            $dataLama = [
                'role' => $user->role,
                'is_active' => $user->is_active,
            ];

            $updateData = [
                'role' => $this->accountRole,
                'is_active' => $this->accountActive,
            ];

            if (!empty($this->newPassword)) {
                $updateData['password'] = Hash::make($this->newPassword);
            }

            if (!empty($this->newPin)) {
                $updateData['pin_absen'] = Hash::make($this->newPin);
            }

            $user->update($updateData);

            AuditLog::catat(
                'UPDATE_AKUN_USER',
                'users',
                (string) $user->id_user,
                $dataLama,
                [
                    'role' => $this->accountRole,
                    'is_active' => $this->accountActive,
                    'password_changed' => !empty($this->newPassword),
                    'pin_changed' => !empty($this->newPin),
                ]
            );

            session()->flash('success', "Akun pengguna {$karyawan->nama_karyawan} berhasil diperbarui.");
        } else {
            $user = User::create([
                'id_karyawan' => $karyawan->id_karyawan,
                'password' => Hash::make($this->newPassword ?: 'password'),
                'pin_absen' => Hash::make($this->newPin ?: '123456'),
                'role' => $this->accountRole,
                'is_active' => $this->accountActive,
            ]);

            AuditLog::catat(
                'CREATE_AKUN_USER',
                'users',
                (string) $user->id_user,
                null,
                [
                    'id_karyawan' => $karyawan->id_karyawan,
                    'role' => $this->accountRole,
                    'is_active' => $this->accountActive,
                ]
            );

            session()->flash('success', "Akun login baru untuk {$karyawan->nama_karyawan} berhasil dibuat.");
        }

        $this->closeAccountModal();
    }

    public function resetPasswordDefault(): void
    {
        $karyawan = Karyawan::with('user')->findOrFail($this->selectedKaryawanId);
        $this->authorize('update', $karyawan);

        if ($karyawan->user) {
            $karyawan->user->update([
                'password' => Hash::make('password'),
            ]);

            AuditLog::catat(
                'RESET_PASSWORD',
                'users',
                (string) $karyawan->user->id_user,
                null,
                ['password_reset' => 'default_password']
            );

            session()->flash('success', "Password login {$karyawan->nama_karyawan} berhasil direset ke password standar ('password').");
        }
    }

    public function resetPinDefault(): void
    {
        $karyawan = Karyawan::with('user')->findOrFail($this->selectedKaryawanId);
        $this->authorize('update', $karyawan);

        if ($karyawan->user) {
            $karyawan->user->update([
                'pin_absen' => Hash::make('123456'),
            ]);

            AuditLog::catat(
                'RESET_PIN_ABSEN',
                'users',
                (string) $karyawan->user->id_user,
                null,
                ['pin_reset' => 'default_pin_6_digit']
            );

            session()->flash('success', "PIN absen kiosk {$karyawan->nama_karyawan} berhasil direset ke PIN standar 6-digit ('123456').");
        }
    }

    public function render()
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        $query = Karyawan::query()
            ->with(['departemen', 'jabatan', 'grup', 'user'])
            ->when($user && ($user->isHod() || $user->isSupervisor()), function ($q) use ($user) {
                $q->where('id_departemen', $user->karyawan?->id_departemen);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_karyawan', 'like', '%' . $this->search . '%')
                        ->orWhere('id_karyawan', 'like', '%' . $this->search . '%')
                        ->orWhere('no_hp', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterDepartemen, fn($q) => $q->where('id_departemen', $this->filterDepartemen))
            ->when($this->filterGrup !== '', function ($q) {
                if ($this->filterGrup === 'null') {
                    $q->whereNull('id_grup');
                } else {
                    $q->where('id_grup', $this->filterGrup);
                }
            })
            ->when($this->filterStatus, fn($q) => $q->where('status_karyawan', $this->filterStatus));

        $karyawans = $query->orderBy('id_karyawan', 'asc')->paginate(12);

        // KPI Counts
        $totalKaryawan = Karyawan::count();
        $totalRegu = Karyawan::whereNotNull('id_grup')->count();
        $totalGs = Karyawan::whereNull('id_grup')->count();
        $totalDept = Departemen::count();

        // Dropdown data
        $departemens = Departemen::orderBy('nama_departemen')->get();
        $jabatans = $this->id_departemen 
            ? Jabatan::where('id_departemen', $this->id_departemen)->orderBy('level_jabatan')->get() 
            : collect();
        $grups = GrupKaryawan::orderBy('nama_grup')->get();

        return view('livewire.master-karyawan', [
            'karyawans' => $karyawans,
            'totalKaryawan' => $totalKaryawan,
            'totalRegu' => $totalRegu,
            'totalGs' => $totalGs,
            'totalDept' => $totalDept,
            'departemens' => $departemens,
            'jabatans' => $jabatans,
            'grups' => $grups,
        ]);
    }
}
