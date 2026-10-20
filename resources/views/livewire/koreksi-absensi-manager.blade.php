<div>
    <!-- Top Breadcrumb & Action Hub -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Plant 1 - Karawang Barat</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span>Monitoring Absensi</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-semibold">Koreksi Absensi Manual</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1 flex-wrap">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Koreksi Absensi Manual &amp; Audit Trail</h1>
                <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold tracking-wider uppercase">
                    Kepatuhan Regulasi BR-12
                </span>
            </div>
            <p class="text-sm text-slate-500 max-w-4xl">
                Koreksi data tap masuk, pulang, atau status absensi operator pabrik. Setiap perubahan wajib mencantumkan alasan tertulis dan tercatat di audit log sistem.
            </p>
        </div>
    </div>

    <!-- Filters & Toolbar -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto flex-wrap">
            <!-- Tanggal Shift -->
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-bold text-slate-600">Tanggal:</span>
                <input 
                    wire:model.live="filterTanggal" 
                    type="date" 
                    class="h-10 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
                >
            </div>

            <!-- Departemen -->
            <select 
                wire:model.live="filterDepartemen" 
                class="h-10 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
            >
                <option value="">Semua Departemen</option>
                @foreach ($departemens as $d)
                    <option value="{{ $d->id_departemen }}">{{ $d->nama_departemen }}</option>
                @endforeach
            </select>

            <!-- Status -->
            <select 
                wire:model.live="filterStatus" 
                class="h-10 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
            >
                <option value="">Semua Status</option>
                <option value="HADIR">HADIR</option>
                <option value="TERLAMBAT">TERLAMBAT</option>
                <option value="ALPA">ALPA</option>
                <option value="IZIN">IZIN</option>
                <option value="SAKIT">SAKIT</option>
                <option value="CUTI">CUTI</option>
                <option value="LIBUR">LIBUR</option>
            </select>
        </div>

        <!-- Search Input -->
        <div class="relative w-full md:w-72">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 flex items-center">
                <span class="material-symbols-outlined text-[18px]">search</span>
            </span>
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari NIK / Nama..." 
                class="w-full h-10 pl-9 pr-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
            >
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-800">
                <thead class="bg-slate-50 text-slate-600 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Operator</th>
                        <th class="py-3.5 px-4">Departemen</th>
                        <th class="py-3.5 px-4">Shift Terjadwal</th>
                        <th class="py-3.5 px-4">Waktu Masuk</th>
                        <th class="py-3.5 px-4">Waktu Pulang</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Koreksi Terakhir</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($absensiList as $abs)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ substr($abs->karyawan->nama_karyawan, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900 leading-tight">{{ $abs->karyawan->nama_karyawan }}</span>
                                        <span class="text-xs text-slate-500 font-mono mt-0.5">{{ $abs->karyawan->id_karyawan }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-semibold text-slate-700">
                                {{ $abs->karyawan->departemen->kode_departemen }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded text-xs font-bold
                                    @if($abs->jadwal->kode_shift === 'S1') bg-amber-100 text-amber-900 border border-amber-300
                                    @elseif($abs->jadwal->kode_shift === 'S2') bg-orange-100 text-orange-950 border border-orange-300
                                    @elseif($abs->jadwal->kode_shift === 'S3') bg-indigo-100 text-indigo-950 border border-indigo-300
                                    @elseif($abs->jadwal->kode_shift === 'GS') bg-blue-100 text-blue-900 border border-blue-300
                                    @else bg-slate-100 text-slate-700 border border-slate-300 @endif
                                ">
                                    {{ $abs->jadwal->kode_shift }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs">
                                {{ $abs->waktu_masuk ? $abs->waktu_masuk->format('H:i:s') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs">
                                {{ $abs->waktu_pulang ? $abs->waktu_pulang->format('H:i:s') : '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                    @if($abs->status === 'HADIR') bg-emerald-50 text-emerald-700 border border-emerald-200
                                    @elseif($abs->status === 'TERLAMBAT') bg-amber-50 text-amber-800 border border-amber-200
                                    @elseif($abs->status === 'ALPA') bg-rose-50 text-rose-700 border border-rose-200
                                    @else bg-blue-50 text-blue-700 border border-blue-200 @endif
                                ">
                                    {{ $abs->status }}
                                    @if($abs->menit_terlambat > 0)
                                        <span class="text-[10px]">({{ $abs->menit_terlambat }}m)</span>
                                    @endif
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-500">
                                @if ($abs->dikoreksi_oleh)
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-slate-700">Oleh: {{ $abs->korektor?->nama ?? 'Admin' }}</span>
                                        <span class="text-[11px] italic truncate max-w-[150px]">"{{ $abs->catatan }}"</span>
                                    </div>
                                @else
                                    <span class="text-slate-400">Belum pernah</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <button 
                                    wire:click="openKoreksiModal({{ $abs->id_absensi }})"
                                    type="button" 
                                    class="h-8 px-3 rounded-lg bg-slate-100 hover:bg-[#0F4C81] hover:text-white text-slate-700 font-semibold text-xs transition-all flex items-center gap-1 mx-auto cursor-pointer"
                                >
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                    <span>Koreksi</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <span class="material-symbols-outlined text-[48px] text-slate-300">fact_check</span>
                                <p class="text-base font-semibold mt-2">Tidak ada data absensi untuk filter ini</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $absensiList->links() }}
        </div>
    </div>

    <!-- MODAL KOREKSI ABSENSI -->
    @if ($isModalOpen && $selectedAbsensi)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
                <div class="h-16 px-6 border-b border-slate-200 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0F4C81] text-[24px]">edit_note</span>
                        <h3 class="font-bold text-slate-900 text-base">Koreksi Absensi Manual</h3>
                    </div>
                    <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="simpanKoreksi" class="p-6 flex flex-col gap-4 text-xs">
                    <!-- Operator Info Card -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-900 text-sm">{{ $selectedAbsensi->karyawan->nama_karyawan }}</span>
                            <span class="text-slate-500">{{ $selectedAbsensi->karyawan->id_karyawan }} &bull; {{ $selectedAbsensi->karyawan->departemen->nama_departemen }}</span>
                        </div>
                        <span class="px-2.5 py-1 rounded text-xs font-bold bg-blue-100 text-blue-900 border border-blue-200">
                            Shift {{ $selectedAbsensi->jadwal->kode_shift }} ({{ $selectedAbsensi->tanggal_shift->format('d-m-Y') }})
                        </span>
                    </div>

                    <!-- Waktu Masuk & Pulang -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-slate-700">Waktu Masuk</label>
                            <input 
                                wire:model="waktu_masuk" 
                                type="datetime-local" 
                                class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]"
                            >
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-slate-700">Waktu Pulang</label>
                            <input 
                                wire:model="waktu_pulang" 
                                type="datetime-local" 
                                class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]"
                            >
                        </div>
                    </div>

                    <!-- Status & Menit Terlambat -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-slate-700">Status Kehadiran *</label>
                            <select wire:model="status" class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]">
                                <option value="HADIR">HADIR</option>
                                <option value="TERLAMBAT">TERLAMBAT</option>
                                <option value="ALPA">ALPA</option>
                                <option value="IZIN">IZIN</option>
                                <option value="SAKIT">SAKIT</option>
                                <option value="CUTI">CUTI</option>
                                <option value="LIBUR">LIBUR</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-slate-700">Menit Terlambat</label>
                            <input 
                                wire:model="menit_terlambat" 
                                type="number" 
                                min="0" 
                                class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]"
                            >
                        </div>
                    </div>

                    <!-- Pulang Cepat Checkbox -->
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="pulang_cepat" type="checkbox" class="w-4 h-4 rounded text-[#0F4C81] focus:ring-[#0F4C81] border-slate-300">
                        <span class="font-semibold text-slate-700">Tandai Pulang Cepat</span>
                    </label>

                    <!-- Alasan Koreksi (Mandatory BR-12) -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700 flex items-center justify-between">
                            <span>Alasan Koreksi (Wajib Diisi - Audit Log) *</span>
                            <span class="text-rose-500 text-[10px] uppercase font-bold">Wajib BR-12</span>
                        </label>
                        <textarea 
                            wire:model="alasan" 
                            rows="3" 
                            placeholder="Contoh: Mesin tap kiosk di Line 3 offline, supervisor mengonfirmasi kehadiran operator secara fisik..." 
                            class="p-3 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-[#0F4C81]"
                        ></textarea>
                        @error('alasan') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                        <button 
                            wire:click="$set('isModalOpen', false)" 
                            type="button" 
                            class="h-10 px-4 rounded-xl bg-white border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="h-10 px-5 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-semibold text-xs shadow-sm transition-all"
                        >
                            Simpan Koreksi &amp; Tulis Audit Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
