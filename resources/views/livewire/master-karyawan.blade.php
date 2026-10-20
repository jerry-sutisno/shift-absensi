<div>
    <!-- Top Breadcrumb & Action Hub -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Plant 1 - Karawang Barat</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span>Administrasi & HR</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-semibold">Master Data Karyawan</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Master Data Karyawan Pabrik</h1>
            <p class="text-sm text-slate-500">
                Basis data terpusat profil operator, teknisi, supervisor, penempatan lini produksi, dan pengelompokan regu kerja.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <button 
                type="button" 
                class="h-10 px-4 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                <span>Import Excel / CSV</span>
            </button>
            <button 
                type="button" 
                class="h-10 px-4 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Export Data</span>
            </button>
            <button 
                wire:click="openCreateDrawer" 
                type="button" 
                class="h-10 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-semibold text-xs shadow-md hover:shadow-lg active:scale-[0.98] transition-all flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>+ Tambah Karyawan</span>
            </button>
        </div>
    </div>

    <!-- KPI Metrics Banner -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <!-- Total Karyawan -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Karyawan</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalKaryawan }}</span>
                <span class="text-xs text-emerald-600 font-medium mt-0.5">100% data aktif</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#0F4C81] flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">groups</span>
            </div>
        </div>

        <!-- Shift Regu -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Karyawan Shift Regu</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalRegu }}</span>
                <span class="text-xs text-blue-600 font-medium mt-0.5">Rotasi Grup A - D</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">sync_alt</span>
            </div>
        </div>

        <!-- Staf General Shift -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Staf General Shift</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalGs }}</span>
                <span class="text-xs text-amber-600 font-medium mt-0.5">Non-Rotasi (GS)</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">business_center</span>
            </div>
        </div>

        <!-- Unit & Departemen -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Departemen Pabrik</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalDept }}</span>
                <span class="text-xs text-slate-500 font-medium mt-0.5">PRS, LGS, PRD, QCT, PCKG</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">domain</span>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Search Input -->
        <div class="relative w-full md:w-80">
            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 flex items-center">
                <span class="material-symbols-outlined text-[20px]">search</span>
            </span>
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari ID, Nama, No HP..." 
                class="w-full h-11 pl-10 pr-4 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81] focus:border-transparent transition-all"
            >
        </div>

        <!-- Dropdowns Filter -->
        <div class="flex items-center gap-2.5 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
            <!-- Filter Departemen -->
            <select 
                wire:model.live="filterDepartemen" 
                class="h-11 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
            >
                <option value="">Semua Departemen</option>
                @foreach ($departemens as $dept)
                    <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }} ({{ $dept->kode_departemen }})</option>
                @endforeach
            </select>

            <!-- Filter Grup -->
            <select 
                wire:model.live="filterGrup" 
                class="h-11 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
            >
                <option value="">Semua Grup</option>
                @foreach ($grups as $g)
                    <option value="{{ $g->id_grup }}">{{ $g->nama_grup }}</option>
                @endforeach
                <option value="null">Non-Regu (GS Tetap)</option>
            </select>

            <!-- Filter Status -->
            <select 
                wire:model.live="filterStatus" 
                class="h-11 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
            >
                <option value="">Semua Status</option>
                <option value="AKTIF">Aktif</option>
                <option value="NONAKTIF">Nonaktif</option>
            </select>

            @if ($search || $filterDepartemen || $filterGrup !== '' || $filterStatus)
                <button 
                    wire:click="$set('search', ''); $set('filterDepartemen', ''); $set('filterGrup', ''); $set('filterStatus', '');" 
                    class="h-11 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition-colors flex items-center gap-1 shrink-0"
                    title="Reset Filter"
                >
                    <span class="material-symbols-outlined text-[16px]">close</span>
                    <span>Reset</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Main Data Table Container -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-800">
                <thead class="bg-slate-50 text-slate-600 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">ID Karyawan</th>
                        <th class="py-3.5 px-4">Nama Lengkap</th>
                        <th class="py-3.5 px-4">Departemen</th>
                        <th class="py-3.5 px-4">Jabatan & Level</th>
                        <th class="py-3.5 px-4">Regu Kerja</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($karyawans as $k)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- ID Karyawan -->
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                {{ $k->id_karyawan }}
                            </td>

                            <!-- Nama Karyawan & Subtitle -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ substr($k->nama_karyawan, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900 leading-tight">{{ $k->nama_karyawan }}</span>
                                        <span class="text-xs text-slate-500 mt-0.5">
                                            {{ $k->jns_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }} 
                                            @if($k->no_hp) &bull; {{ $k->no_hp }} @endif
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Departemen -->
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-semibold text-xs">
                                    {{ $k->departemen->kode_departemen }} &bull; {{ $k->departemen->nama_departemen }}
                                </span>
                            </td>

                            <!-- Jabatan & Level Badge -->
                            <td class="py-3.5 px-4">
                                <div class="flex flex-col">
                                    <span class="font-medium text-slate-900">{{ $k->jabatan->nama_jabatan }}</span>
                                    <div class="mt-1">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider
                                            @if($k->jabatan->level_jabatan === 'DIREKTUR') bg-purple-100 text-purple-900 border border-purple-200
                                            @elseif($k->jabatan->level_jabatan === 'HOD') bg-amber-100 text-amber-900 border border-amber-200
                                            @elseif($k->jabatan->level_jabatan === 'SUPERVISOR') bg-orange-100 text-orange-950 border border-orange-200
                                            @else bg-blue-100 text-blue-900 border border-blue-200 @endif
                                        ">
                                            {{ $k->jabatan->level_jabatan }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Regu / Grup Kerja -->
                            <td class="py-3.5 px-4">
                                @if ($k->grup)
                                    <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 font-bold text-xs flex items-center gap-1 w-fit">
                                        <span class="material-symbols-outlined text-[14px]">groups</span>
                                        <span>{{ $k->grup->nama_grup }}</span>
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-semibold text-xs flex items-center gap-1 w-fit">
                                        <span class="material-symbols-outlined text-[14px]">badge</span>
                                        <span>General Shift (GS)</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                @if ($k->isAktif())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button 
                                        wire:click="openEditDrawer('{{ $k->id_karyawan }}')" 
                                        title="Ubah data karyawan" 
                                        class="p-2 rounded-lg text-slate-500 hover:text-[#0F4C81] hover:bg-slate-100 transition-colors cursor-pointer"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>
                                    <button 
                                        wire:click="toggleStatus('{{ $k->id_karyawan }}')" 
                                        title="{{ $k->isAktif() ? 'Nonaktifkan karyawan' : 'Aktifkan karyawan' }}" 
                                        class="p-2 rounded-lg transition-colors cursor-pointer {{ $k->isAktif() ? 'text-slate-400 hover:text-amber-600 hover:bg-amber-50' : 'text-slate-400 hover:text-emerald-600 hover:bg-emerald-50' }}"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">{{ $k->isAktif() ? 'person_off' : 'person_check' }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-[48px] text-slate-300">search_off</span>
                                    <span class="text-base font-semibold">Tidak ada karyawan yang cocok</span>
                                    <span class="text-xs text-slate-400">Coba ubah kata kunci pencarian atau reset filter.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
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
            x-transition:enter="transition-opacity ease-linear duration-300" 
            x-transition:enter-start="opacity-0" 
            x-transition:enter-end="opacity-100" 
            x-transition:leave="transition-opacity ease-linear duration-300" 
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0" 
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs"
            @click="$wire.closeDrawer()"
        ></div>

        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div 
                        x-show="show" 
                        x-transition:enter="transform transition ease-in-out duration-300 sm:duration-400" 
                        x-transition:enter-start="translate-x-full" 
                        x-transition:enter-end="translate-x-0" 
                        x-transition:leave="transform transition ease-in-out duration-300 sm:duration-400" 
                        x-transition:leave-start="translate-x-0" 
                        x-transition:leave-end="translate-x-full" 
                        class="pointer-events-auto w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between"
                    >
                        <!-- Drawer Header (Matches Stitch Layar 9) -->
                        <div class="h-16 px-6 border-b border-slate-200 flex items-center justify-between bg-white">
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-slate-900 text-base leading-tight">Data Karyawan</h3>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase {{ $isEditMode ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                        {{ $isEditMode ? 'Mode Edit' : 'Mode Tambah' }}
                                    </span>
                                </div>
                                <span class="text-xs text-slate-500 mt-0.5">
                                    NIK Terpilih: <span class="font-mono font-semibold text-slate-800">{{ $id_karyawan ?: 'Karyawan Baru' }}</span>
                                </span>
                            </div>
                            <button 
                                wire:click="closeDrawer" 
                                type="button" 
                                class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                            >
                                <span class="material-symbols-outlined text-[22px]">close</span>
                            </button>
                        </div>

                        <!-- Drawer Body Form -->
                        <form wire:submit.prevent="save" id="drawer-form" class="flex-1 overflow-y-auto p-6 flex flex-col gap-4">
                            <!-- ID Karyawan -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    ID Karyawan / NIK <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    wire:model="id_karyawan" 
                                    type="text" 
                                    placeholder="Contoh: EMP-0033" 
                                    class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-[#0F4C81] focus:outline-none {{ $isEditMode ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white' }}"
                                    {{ $isEditMode ? 'readonly' : '' }}
                                >
                                @error('id_karyawan') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Nama Karyawan -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    wire:model="nama_karyawan" 
                                    type="text" 
                                    placeholder="Nama karyawan sesuai KTP..." 
                                    class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-[#0F4C81] focus:outline-none"
                                >
                                @error('nama_karyawan') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Gender & Tanggal Lahir (2 Cols) -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                        Jenis Kelamin <span class="text-rose-500">*</span>
                                    </label>
                                    <select wire:model="jns_kelamin" class="w-full h-10 px-3 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-[#0F4C81] focus:outline-none">
                                        <option value="L">Laki-laki (L)</option>
                                        <option value="P">Perempuan (P)</option>
                                    </select>
                                    @error('jns_kelamin') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                        Tanggal Lahir <span class="text-rose-500">*</span>
                                    </label>
                                    <input 
                                        wire:model="tgl_lahir" 
                                        type="date" 
                                        class="w-full h-10 px-3 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-[#0F4C81] focus:outline-none"
                                    >
                                    @error('tgl_lahir') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- No HP -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    No. Handphone / WhatsApp
                                </label>
                                <input 
                                    wire:model="no_hp" 
                                    type="text" 
                                    placeholder="Contoh: 081234567890" 
                                    class="w-full h-10 px-3.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-[#0F4C81] focus:outline-none"
                                >
                                @error('no_hp') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Departemen -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Departemen <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    wire:model.live="id_departemen" 
                                    class="w-full h-10 px-3 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-[#0F4C81] focus:outline-none"
                                >
                                    <option value="">Pilih Departemen...</option>
                                    @foreach ($departemens as $dept)
                                        <option value="{{ $dept->id_departemen }}">{{ $dept->nama_departemen }} ({{ $dept->kode_departemen }})</option>
                                    @endforeach
                                </select>
                                @error('id_departemen') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Jabatan (Dependent) -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Jabatan & Level <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    wire:model="id_jabatan" 
                                    class="w-full h-10 px-3 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-[#0F4C81] focus:outline-none"
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
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Regu / Grup Rotasi
                                </label>
                                <select 
                                    wire:model="id_grup" 
                                    class="w-full h-10 px-3 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-[#0F4C81] focus:outline-none"
                                >
                                    <option value="">Non-Regu / General Shift (GS)</option>
                                    @foreach ($grups as $g)
                                        <option value="{{ $g->id_grup }}">{{ $g->nama_grup }}</option>
                                    @endforeach
                                </select>
                                <span class="text-[11px] text-slate-500 mt-0.5 block">Karyawan tanpa grup akan otomatis terjadwal GS Senin-Jumat (OFF Sabtu-Minggu).</span>
                                @error('id_grup') <span class="text-xs text-rose-600 font-medium mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Role Akun Pengguna -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                                    Role Akses Sistem <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    wire:model="role" 
                                    class="w-full h-10 px-3 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-[#0F4C81] focus:outline-none"
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
                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-slate-800">Status Kepegawaian</span>
                                    <span class="text-xs text-slate-500">Karyawan nonaktif tidak bisa login & tidak dijadwalkan.</span>
                                </div>
                                <select wire:model="status_karyawan" class="h-9 px-3 rounded-lg border border-slate-300 text-xs font-bold">
                                    <option value="AKTIF">AKTIF</option>
                                    <option value="NONAKTIF">NONAKTIF</option>
                                </select>
                            </div>

                            @if (!$isEditMode)
                                <div class="p-3 rounded-xl bg-blue-50 border border-blue-200 text-xs text-blue-900 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-blue-600 text-[18px]">key</span>
                                    <span>Akun login otomatis dibuat (Password default: <b>password</b>, PIN: <b>123456</b>).</span>
                                </div>
                            @endif
                        </form>

                        <!-- Drawer Footer -->
                        <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-end gap-3">
                            <button 
                                wire:click="closeDrawer" 
                                type="button" 
                                class="h-10 px-4 rounded-xl bg-white border border-slate-300 text-slate-700 text-sm font-semibold hover:bg-slate-100 transition-colors cursor-pointer"
                            >
                                Batal
                            </button>
                            <button 
                                form="drawer-form" 
                                type="submit" 
                                class="h-10 px-5 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white text-sm font-semibold shadow-sm hover:shadow active:scale-[0.98] transition-all flex items-center gap-2 cursor-pointer"
                            >
                                <span class="material-symbols-outlined text-[18px]">save</span>
                                <span>{{ $isEditMode ? 'Perbarui Data' : 'Simpan Karyawan' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
