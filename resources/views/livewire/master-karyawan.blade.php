<div>
    <!-- Top Action Hub -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-0.5">
            <h1 class="text-xl font-semibold text-slate-900">Master Data Karyawan Pabrik</h1>
            <p class="text-xs text-slate-500">
                Pengelolaan data operator, teknisi, supervisor, penempatan lini, dan regu kerja.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <button 
                type="button" 
                class="h-9 px-3.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                <span>Impor</span>
            </button>
            <button 
                type="button" 
                class="h-9 px-3.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Ekspor</span>
            </button>
            <button 
                wire:click="openCreateDrawer" 
                type="button" 
                class="h-9 px-3.5 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>+ Tambah Karyawan</span>
            </button>
        </div>
    </div>

    <!-- Metrics Cards (Clean minimal metrics, no circular colored icon containers) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <!-- Total Karyawan -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Total Karyawan</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ $totalKaryawan }}</span>
            <span class="text-xs text-slate-500 mt-1">Seluruh data aktif</span>
        </div>

        <!-- Shift Regu -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Karyawan Shift Regu</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ $totalRegu }}</span>
            <span class="text-xs text-slate-500 mt-1">Rotasi Grup A - D</span>
        </div>

        <!-- Staf General Shift -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Staf General Shift</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ $totalGs }}</span>
            <span class="text-xs text-slate-500 mt-1">Jadwal tetap Senin - Jumat</span>
        </div>

        <!-- Unit & Departemen -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Departemen Pabrik</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ $totalDept }}</span>
            <span class="text-xs text-slate-500 mt-1">PRS, LGS, PRD, QCT, PCKG</span>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
        <!-- Search Input -->
        <div class="relative w-full md:w-80">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 flex items-center">
                <span class="material-symbols-outlined text-[18px]">search</span>
            </span>
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari ID, nama karyawan..." 
                class="w-full h-10 pl-9 pr-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150"
            >
        </div>

        <!-- Dropdowns Filter -->
        <div class="flex items-center gap-2.5 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
            <!-- Filter Departemen -->
            <select 
                wire:model.live="filterDepartemen" 
                class="h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs font-normal text-slate-700 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81]"
            >
                <option value="">Semua Departemen</option>
                @foreach ($departemens as $dept)
                    <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }} ({{ $dept->kode_departemen }})</option>
                @endforeach
            </select>

            <!-- Filter Grup -->
            <select 
                wire:model.live="filterGrup" 
                class="h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs font-normal text-slate-700 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81]"
            >
                <option value="">Semua Regu</option>
                @foreach ($grups as $g)
                    <option value="{{ $g->id_grup }}">{{ $g->nama_grup }}</option>
                @endforeach
                <option value="null">Non-Regu (GS)</option>
            </select>

            <!-- Filter Status -->
            <select 
                wire:model.live="filterStatus" 
                class="h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs font-normal text-slate-700 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81]"
            >
                <option value="">Semua Status</option>
                <option value="AKTIF">Aktif</option>
                <option value="NONAKTIF">Nonaktif</option>
            </select>

            @if ($search || $filterDepartemen || $filterGrup !== '' || $filterStatus)
                <button 
                    wire:click="$set('search', ''); $set('filterDepartemen', ''); $set('filterGrup', ''); $set('filterStatus', '');" 
                    class="h-10 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition-colors duration-150 flex items-center gap-1 shrink-0"
                    title="Reset Filter"
                >
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    <span>Reset</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Main Data Table Container -->
    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-800">
                <thead class="bg-slate-50 text-slate-600 font-medium text-xs border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4 font-medium">ID Karyawan</th>
                        <th class="py-3 px-4 font-medium">Nama Lengkap</th>
                        <th class="py-3 px-4 font-medium">Departemen</th>
                        <th class="py-3 px-4 font-medium">Jabatan &amp; Level</th>
                        <th class="py-3 px-4 font-medium">Regu Kerja</th>
                        <th class="py-3 px-4 font-medium">Status</th>
                        <th class="py-3 px-4 font-medium text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($karyawans as $k)
                        <tr class="hover:bg-slate-50/60 transition-colors duration-150">
                            <!-- ID Karyawan -->
                            <td class="py-3 px-4 font-mono font-medium text-slate-900 text-xs">
                                {{ $k->id_karyawan }}
                            </td>

                            <!-- Nama Karyawan & Subtitle -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-medium text-xs shrink-0">
                                        {{ substr($k->nama_karyawan, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-900 text-xs leading-tight">{{ $k->nama_karyawan }}</span>
                                        <span class="text-[11px] text-slate-400 mt-0.5">
                                            {{ $k->jns_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }} 
                                            @if($k->no_hp) &bull; {{ $k->no_hp }} @endif
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Departemen -->
                            <td class="py-3 px-4 text-xs text-slate-700">
                                {{ $k->departemen->kode_departemen }} &bull; {{ $k->departemen->nama_departemen }}
                            </td>

                            <!-- Jabatan & Level Badge -->
                            <td class="py-3 px-4">
                                <div class="flex flex-col">
                                    <span class="text-xs text-slate-900">{{ $k->jabatan->nama_jabatan }}</span>
                                    <div class="mt-0.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $k->jabatan->level_jabatan }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Regu / Grup Kerja -->
                            <td class="py-3 px-4">
                                @if ($k->grup)
                                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200 font-medium text-xs">
                                        {{ $k->grup->nama_grup }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-medium text-xs">
                                        General Shift (GS)
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4">
                                @if ($k->isAktif())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if(Auth::user()->isAdminHr())
                                        <button 
                                            wire:click="openAccountModal('{{ $k->id_karyawan }}')" 
                                            title="Kelola Akun & PIN Absen" 
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-[#0F4C81] hover:bg-blue-50 transition-colors duration-150 cursor-pointer"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">key</span>
                                        </button>
                                    @endif
                                    <button 
                                        wire:click="openEditDrawer('{{ $k->id_karyawan }}')" 
                                        title="Ubah data" 
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors duration-150 cursor-pointer"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button 
                                        wire:click="toggleStatus('{{ $k->id_karyawan }}')" 
                                        title="{{ $k->isAktif() ? 'Nonaktifkan karyawan' : 'Aktifkan karyawan' }}" 
                                        class="p-1.5 rounded-lg transition-colors duration-150 cursor-pointer {{ $k->isAktif() ? 'text-slate-400 hover:text-amber-600 hover:bg-amber-50' : 'text-slate-400 hover:text-emerald-600 hover:bg-emerald-50' }}"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">{{ $k->isAktif() ? 'person_off' : 'person_check' }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-xs text-slate-500">
                                Tidak ada karyawan yang cocok dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-5 py-3 border-t border-slate-200 bg-slate-50">
            {{ $karyawans->links() }}
        </div>
    </div>

    <!-- Slide-over Drawer (Create / Edit Form) -->
    @if ($isDrawerOpen)
    <div 
        x-data="{ show: true }" 
        x-show="show" 
        class="relative z-50"
    >
        <!-- Backdrop -->
        <div 
            x-show="show" 
            x-transition:enter="transition-opacity ease-linear duration-150" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="transition-opacity ease-linear duration-150" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0" 
            class="fixed inset-0 bg-slate-900/40"
            @click="$wire.closeDrawer()"
        ></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div 
                        x-show="show" 
                        x-transition:enter="transform transition ease-in-out duration-200" 
                        x-transition:enter-start="translate-x-full" 
                        x-transition:enter-end="translate-x-0" 
                        x-transition:leave="transform transition ease-in-out duration-200" 
                        x-transition:leave-start="translate-x-0" 
                        x-transition:leave-end="translate-x-full" 
                        class="pointer-events-auto w-screen max-w-md bg-white border-l border-slate-200 shadow-xl flex flex-col justify-between"
                    >
                        <!-- Drawer Header -->
                        <div class="h-14 px-6 border-b border-slate-200 flex items-center justify-between bg-white">
                            <div class="flex items-center gap-2">
                                <h3 class="font-medium text-slate-900 text-sm">{{ $isEditMode ? 'Edit Data Karyawan' : 'Tambah Karyawan' }}</h3>
                                @if($isEditMode)
                                    <span class="font-mono text-xs text-slate-400">({{ $id_karyawan }})</span>
                                @endif
                            </div>
                            <button 
                                wire:click="closeDrawer" 
                                type="button" 
                                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors duration-150 cursor-pointer"
                            >
                                <span class="material-symbols-outlined text-[20px]">close</span>
                            </button>
                        </div>

                        <!-- Drawer Body Form -->
                        <form wire:submit.prevent="save" id="drawer-form" class="flex-1 overflow-y-auto p-6 flex flex-col gap-4">
                            <!-- ID Karyawan -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    ID Karyawan <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    wire:model="id_karyawan" 
                                    type="text" 
                                    placeholder="Contoh: EMP-0033" 
                                    class="w-full h-10 px-3 rounded-xl border border-slate-200 text-xs focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] focus:outline-none transition-colors duration-150 {{ $isEditMode ? 'bg-slate-50 text-slate-500 cursor-not-allowed' : 'bg-white' }}"
                                    {{ $isEditMode ? 'readonly' : '' }}
                                >
                                @error('id_karyawan') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Nama Karyawan -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    wire:model="nama_karyawan" 
                                    type="text" 
                                    placeholder="Nama karyawan sesuai identitas" 
                                    class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] focus:outline-none transition-colors duration-150"
                                >
                                @error('nama_karyawan') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Gender & Tanggal Lahir (2 Cols) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">
                                        Jenis Kelamin <span class="text-rose-500">*</span>
                                    </label>
                                    <select wire:model="jns_kelamin" class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] focus:outline-none transition-colors duration-150">
                                        <option value="L">Laki-laki (L)</option>
                                        <option value="P">Perempuan (P)</option>
                                    </select>
                                    @error('jns_kelamin') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 mb-1">
                                        Tanggal Lahir <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        wire:model="tgl_lahir" 
                                        type="date" 
                                        class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] focus:outline-none transition-colors duration-150"
                                    >
                                    @error('tgl_lahir') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- No HP -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    No. Handphone
                                </label>
                                <input 
                                    wire:model="no_hp" 
                                    type="text" 
                                    placeholder="Contoh: 081234567890" 
                                    class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] focus:outline-none transition-colors duration-150"
                                >
                                @error('no_hp') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Departemen -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Departemen <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    wire:model.live="id_departemen" 
                                    class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] focus:outline-none transition-colors duration-150"
                                >
                                    <option value="">Pilih Departemen...</option>
                                    @foreach ($departemens as $dept)
                                        <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }} ({{ $dept->kode_departemen }})</option>
                                    @endforeach
                                </select>
                                @error('id_departemen') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Jabatan -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Jabatan &amp; Level <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    wire:model="id_jabatan" 
                                    class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] focus:outline-none transition-colors duration-150"
                                >
                                    <option value="">Pilih Jabatan...</option>
                                    @foreach ($jabatans as $jab)
                                        <option value="{{ $jab->id_jabatan }}">{{ $jab->nama_jabatan }} [{{ $jab->level_jabatan }}]</option>
                                    @endforeach
                                </select>
                                @error('id_jabatan') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Grup Karyawan -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Regu / Grup Rotasi
                                </label>
                                <select 
                                    wire:model="id_grup" 
                                    class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] focus:outline-none transition-colors duration-150"
                                >
                                    <option value="">Non-Regu / General Shift (GS)</option>
                                    @foreach ($grups as $g)
                                        <option value="{{ $g->id_grup }}">{{ $g->nama_grup }}</option>
                                    @endforeach
                                </select>
                                <span class="text-[11px] text-slate-400 mt-0.5 block">Karyawan tanpa grup akan otomatis terjadwal GS Senin-Jumat.</span>
                                @error('id_grup') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Role Akun Pengguna -->
                            <div>
                                <label class="block text-xs font-medium text-slate-700 mb-1">
                                    Role Akses Sistem <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    wire:model="role" 
                                    class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] focus:outline-none transition-colors duration-150"
                                >
                                    <option value="OPERATOR">OPERATOR</option>
                                    <option value="SUPERVISOR">SUPERVISOR</option>
                                    <option value="HOD">HOD (Head of Department)</option>
                                    <option value="DIREKTUR">DIREKTUR</option>
                                    <option value="ADMIN_HR">ADMIN_HR (Personalia)</option>
                                </select>
                                @error('role') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status Aktif / Nonaktif Toggle -->
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-xs font-medium text-slate-800">Status Kepegawaian</span>
                                    <span class="text-[11px] text-slate-400">Karyawan nonaktif tidak dapat mengakses sistem.</span>
                                </div>
                                <select wire:model="status_karyawan" class="h-9 px-2.5 rounded-xl border border-slate-200 text-xs font-medium bg-white">
                                    <option value="AKTIF">Aktif</option>
                                    <option value="NONAKTIF">Nonaktif</option>
                                </select>
                            </div>

                            @if (!$isEditMode)
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-slate-500 text-[18px]">key</span>
                                    <span>Kata sandi default: <code class="font-mono text-slate-700">password</code>, PIN: <code class="font-mono text-slate-700">123456</code>.</span>
                                </div>
                            @endif
                        </form>

                        <!-- Drawer Footer -->
                        <div class="p-4 border-t border-slate-200 bg-white flex items-center justify-end gap-2.5">
                            <button 
                                wire:click="closeDrawer" 
                                type="button" 
                                class="h-9 px-3.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-medium hover:bg-slate-50 transition-colors duration-150 cursor-pointer"
                            >
                                Batal
                            </button>
                            <button 
                                form="drawer-form" 
                                type="submit" 
                                class="h-9 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white text-xs font-medium transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
                            >
                                <span class="material-symbols-outlined text-[16px]">save</span>
                                <span>{{ $isEditMode ? 'Perbarui Data' : 'Simpan Karyawan' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Kelola Akun & PIN Absen -->
    @if ($isAccountModalOpen && $selectedKaryawan)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150 flex flex-col">
                <!-- Modal Header -->
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#0F4C81] flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm text-slate-900">Kelola Akun &amp; PIN Absen</h3>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                {{ $selectedKaryawan->nama_karyawan }} &bull; <span class="font-mono">{{ $selectedKaryawan->id_karyawan }}</span>
                            </div>
                        </div>
                    </div>
                    <button wire:click="closeAccountModal" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <form wire:submit.prevent="simpanAkun" id="account-form" class="p-5 flex flex-col gap-4 text-xs">
                    <!-- Status Akun Info -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <span class="text-slate-600 font-medium">Status Akun Login:</span>
                        @if ($selectedKaryawan->user)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Terdaftar ({{ $selectedKaryawan->user->role }})
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                Belum Memiliki Akun
                            </span>
                        @endif
                    </div>

                    <!-- Role Selection -->
                    <div>
                        <label class="block text-slate-700 font-medium mb-1">Peran Akses (Role)</label>
                        <select 
                            wire:model="accountRole" 
                            class="w-full h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81]"
                        >
                            <option value="OPERATOR">OPERATOR - Akses Beranda &amp; Jadwal Sendiri</option>
                            <option value="SUPERVISOR">SUPERVISOR - Verifikasi Pengajuan, Jadwal &amp; Logbook Regu</option>
                            <option value="HOD">HOD - Approval Departemen &amp; Laporan</option>
                            <option value="DIREKTUR">DIREKTUR - Monitoring Eksekutif &amp; Laporan Pabrik</option>
                            <option value="ADMIN_HR">ADMIN_HR - Akses Penuh Konfigurasi &amp; Master Data</option>
                        </select>
                        @error('accountRole') <span class="text-rose-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status User (Aktif / Nonaktif) -->
                    <div>
                        <label class="block text-slate-700 font-medium mb-1">Status Login Akun</label>
                        <select 
                            wire:model="accountActive" 
                            class="w-full h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81]"
                        >
                            <option value="1">Aktif (Dapat Login)</option>
                            <option value="0">Nonaktif (Login Diblokir)</option>
                        </select>
                    </div>

                    <!-- Password Management -->
                    <div class="pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-slate-700 font-medium">Ubah Password Login</label>
                            @if ($selectedKaryawan->user)
                                <button 
                                    type="button" 
                                    wire:click="resetPasswordDefault" 
                                    class="text-[11px] text-[#0F4C81] hover:underline font-medium cursor-pointer"
                                >
                                    Reset ke 'password'
                                </button>
                            @endif
                        </div>
                        <input 
                            wire:model="newPassword" 
                            type="password" 
                            placeholder="Ketik password baru (kosongkan jika tidak diubah)" 
                            class="w-full h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81]"
                        >
                        @error('newPassword') <span class="text-rose-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- PIN Absen Kiosk Management -->
                    <div class="pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-slate-700 font-medium">Ubah PIN Absen Kiosk (6 Digit)</label>
                            @if ($selectedKaryawan->user)
                                <button 
                                    type="button" 
                                    wire:click="resetPinDefault" 
                                    class="text-[11px] text-[#0F4C81] hover:underline font-medium cursor-pointer"
                                >
                                    Reset ke '123456'
                                </button>
                            @endif
                        </div>
                        <input 
                            wire:model="newPin" 
                            type="password" 
                            maxlength="6"
                            placeholder="6 digit angka (kosongkan jika tidak diubah)" 
                            class="w-full h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 font-mono tracking-widest focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81]"
                        >
                        <p class="text-[10px] text-slate-400 mt-1">
                            PIN disimpan sebagai hash terenkripsi dan tidak pernah ditampilkan ke pengguna.
                        </p>
                        @error('newPin') <span class="text-rose-600 text-[11px] mt-0.5 block">{{ $message }}</span> @enderror
                    </div>
                </form>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end gap-2">
                    <button 
                        wire:click="closeAccountModal" 
                        type="button" 
                        class="h-9 px-4 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-100 cursor-pointer"
                    >
                        Batal
                    </button>
                    <button 
                        form="account-form" 
                        type="submit" 
                        class="h-9 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white text-xs font-medium transition-colors cursor-pointer"
                    >
                        Simpan Akun &amp; PIN
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

