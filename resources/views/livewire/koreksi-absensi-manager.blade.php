<div>
    <!-- Top Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Monitoring absensi</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-medium">Koreksi absensi</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1">
                <h1 class="text-xl font-semibold text-slate-900">Koreksi absensi manual</h1>
            </div>
            <p class="text-xs text-slate-500 max-w-3xl">
                Koreksi data waktu masuk, pulang, atau status absensi operator. Setiap perubahan wajib mencantumkan alasan tertulis untuk audit log.
            </p>
        </div>
    </div>

    <!-- Filters & Toolbar -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3 w-full md:w-auto flex-wrap">
            <!-- Tanggal Shift -->
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-medium text-slate-600">Tanggal:</span>
                <input 
                    wire:model.live="filterTanggal" 
                    type="date" 
                    class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
                >
            </div>

            <!-- Departemen -->
            <select 
                wire:model.live="filterDepartemen" 
                class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
            >
                <option value="">Semua departemen</option>
                @foreach ($departemens as $d)
                    <option value="{{ $d->id_departemen }}">{{ $d->nama_departemen }}</option>
                @endforeach
            </select>

            <!-- Status -->
            <select 
                wire:model.live="filterStatus" 
                class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
            >
                <option value="">Semua status</option>
                <option value="HADIR">Hadir</option>
                <option value="TERLAMBAT">Terlambat</option>
                <option value="ALPA">Alpa</option>
                <option value="IZIN">Izin</option>
                <option value="SAKIT">Sakit</option>
                <option value="CUTI">Cuti</option>
                <option value="LIBUR">Libur</option>
            </select>
        </div>

        <!-- Search Input -->
        <div class="relative w-full md:w-64">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 flex items-center">
                <span class="material-symbols-outlined text-[18px]">search</span>
            </span>
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari NIK atau nama..." 
                class="w-full h-9 pl-9 pr-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
            >
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 font-medium text-xs border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Operator</th>
                        <th class="py-3 px-4">Departemen</th>
                        <th class="py-3 px-4">Shift</th>
                        <th class="py-3 px-4">Waktu masuk</th>
                        <th class="py-3 px-4">Waktu pulang</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Koreksi terakhir</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($absensiList as $abs)
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-medium text-xs shrink-0">
                                        {{ substr($abs->karyawan->nama_karyawan, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-900 leading-tight">{{ $abs->karyawan->nama_karyawan }}</span>
                                        <span class="text-[11px] text-slate-500 font-mono">{{ $abs->karyawan->id_karyawan }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $abs->karyawan->departemen->kode_departemen }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium
                                    @if($abs->jadwal->kode_shift === 'S1') bg-amber-50 text-amber-800 border border-amber-200
                                    @elseif($abs->jadwal->kode_shift === 'S2') bg-orange-50 text-orange-800 border border-orange-200
                                    @elseif($abs->jadwal->kode_shift === 'S3') bg-indigo-50 text-indigo-800 border border-indigo-200
                                    @elseif($abs->jadwal->kode_shift === 'GS') bg-sky-50 text-sky-800 border border-sky-200
                                    @else bg-slate-100 text-slate-700 border border-slate-200 @endif
                                ">
                                    {{ $abs->jadwal->kode_shift }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-700">
                                {{ $abs->waktu_masuk ? $abs->waktu_masuk->format('H:i:s') : '-' }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-700">
                                {{ $abs->waktu_pulang ? $abs->waktu_pulang->format('H:i:s') : '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-medium
                                    @if($abs->status === 'HADIR') bg-emerald-50 text-emerald-800 border border-emerald-200
                                    @elseif($abs->status === 'TERLAMBAT') bg-amber-50 text-amber-800 border border-amber-200
                                    @elseif($abs->status === 'ALPA') bg-rose-50 text-rose-800 border border-rose-200
                                    @else bg-sky-50 text-sky-800 border border-sky-200 @endif
                                ">
                                    {{ $abs->status }}
                                    @if($abs->menit_terlambat > 0)
                                        <span class="text-[10px]">({{ $abs->menit_terlambat }}m)</span>
                                    @endif
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                @if ($abs->dikoreksi_oleh)
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-700">Oleh: {{ $abs->korektor?->nama ?? 'Admin' }}</span>
                                        <span class="text-[11px] text-slate-500 truncate max-w-[150px]">"{{ $abs->catatan }}"</span>
                                    </div>
                                @else
                                    <span class="text-slate-400">Belum ada</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @can('koreksi', $abs)
                                    <button 
                                        wire:click="openKoreksiModal({{ $abs->id_absensi }})"
                                        type="button" 
                                        class="h-8 px-2.5 rounded-xl bg-slate-100 hover:bg-[#0F4C81] hover:text-white text-slate-700 font-medium text-xs transition-colors duration-150 inline-flex items-center gap-1 cursor-pointer"
                                    >
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                        <span>Koreksi</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 font-mono">-</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-500">
                                Tidak ada data absensi untuk filter yang dipilih
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50">
            {{ $absensiList->links() }}
        </div>
    </div>

    <!-- MODAL KOREKSI ABSENSI -->
    @if ($isModalOpen && $selectedAbsensi)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
            <div class="w-full max-w-lg bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden flex flex-col">
                <div class="h-14 px-5 border-b border-slate-200 flex items-center justify-between bg-white">
                    <h3 class="font-semibold text-slate-900 text-sm">Koreksi absensi manual</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="simpanKoreksi" class="p-5 flex flex-col gap-4 text-xs">
                    <!-- Operator Info Card -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="font-medium text-slate-900">{{ $selectedAbsensi->karyawan->nama_karyawan }}</span>
                            <span class="text-[11px] text-slate-500">{{ $selectedAbsensi->karyawan->id_karyawan }} &bull; {{ $selectedAbsensi->karyawan->departemen->nama_departemen }}</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-200 text-slate-800">
                            Shift {{ $selectedAbsensi->jadwal->kode_shift }} ({{ $selectedAbsensi->tanggal_shift->format('d-m-Y') }})
                        </span>
                    </div>

                    <!-- Waktu Masuk & Pulang -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="font-medium text-slate-700">Waktu masuk</label>
                            <input 
                                wire:model="waktu_masuk" 
                                type="datetime-local" 
                                class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
                            >
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="font-medium text-slate-700">Waktu pulang</label>
                            <input 
                                wire:model="waktu_pulang" 
                                type="datetime-local" 
                                class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
                            >
                        </div>
                    </div>

                    <!-- Status & Menit Terlambat -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="font-medium text-slate-700">Status kehadiran *</label>
                            <select wire:model="status" class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]">
                                <option value="HADIR">Hadir</option>
                                <option value="TERLAMBAT">Terlambat</option>
                                <option value="ALPA">Alpa</option>
                                <option value="IZIN">Izin</option>
                                <option value="SAKIT">Sakit</option>
                                <option value="CUTI">Cuti</option>
                                <option value="LIBUR">Libur</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="font-medium text-slate-700">Menit terlambat</label>
                            <input 
                                wire:model="menit_terlambat" 
                                type="number" 
                                min="0" 
                                class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
                            >
                        </div>
                    </div>

                    <!-- Pulang Cepat Checkbox -->
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="pulang_cepat" type="checkbox" class="w-4 h-4 rounded text-[#0F4C81] focus:ring-[#0F4C81] border-slate-300">
                        <span class="font-medium text-slate-700">Tandai pulang cepat</span>
                    </label>

                    <!-- Alasan Koreksi (Mandatory BR-12) -->
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">
                            Alasan koreksi (wajib diisi untuk audit log) *
                        </label>
                        <textarea 
                            wire:model="alasan" 
                            rows="3" 
                            placeholder="Contoh: Mesin absen tidak merespons, supervisor mengonfirmasi kehadiran operator secara fisik..." 
                            class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
                        ></textarea>
                        @error('alasan') <span class="text-rose-600 font-medium text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                        <button 
                            wire:click="$set('isModalOpen', false)" 
                            type="button" 
                            class="h-9 px-4 rounded-xl bg-white border border-slate-200 text-slate-700 font-medium text-xs hover:bg-slate-50 transition-colors duration-150"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="h-9 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#0c3c66] text-white font-medium text-xs transition-colors duration-150"
                        >
                            Simpan koreksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
