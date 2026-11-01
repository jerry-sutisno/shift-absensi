<div>
    <!-- Top Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Laporan</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-medium">Rekap Bulanan</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1">
                <h1 class="text-xl font-semibold text-slate-900">Rekapitulasi Absensi Bulanan</h1>
            </div>
            <p class="text-xs text-slate-500 max-w-3xl">
                Akumulasi kehadiran kerja, keterlambatan, alpa, cuti/izin, dan lembur operator per periode bulan.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium">
                Periode: {{ $carbonMonth->translatedFormat('F Y') }}
            </span>
        </div>
    </div>

    <!-- 4 KPI Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="p-4 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Total Hadir</span>
            <span class="text-2xl font-semibold text-emerald-600 mt-2">{{ $kpiHadir }}</span>
            <span class="text-xs text-slate-500 mt-1">Shift terlaksana</span>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Keterlambatan</span>
            <span class="text-2xl font-semibold text-amber-600 mt-2">{{ $kpiTerlambat }}</span>
            <span class="text-xs text-slate-500 mt-1">Insiden terlambat</span>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Alpa / Tanpa Keterangan</span>
            <span class="text-2xl font-semibold text-rose-600 mt-2">{{ $kpiAlpa }}</span>
            <span class="text-xs text-slate-500 mt-1">Tidak memenuhi jadwal</span>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Izin / Sakit / Cuti</span>
            <span class="text-2xl font-semibold text-blue-600 mt-2">{{ $kpiIzinCuti }}</span>
            <span class="text-xs text-slate-500 mt-1">Terverifikasi</span>
        </div>
    </div>

    <!-- Toolbar Filters -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3 w-full md:w-auto flex-wrap">
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-medium text-slate-600">Bulan:</span>
                <input 
                    wire:model.live="filterBulan" 
                    type="month" 
                    class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800"
                >
            </div>

            @if(Auth::user()->isAdminHr() || Auth::user()->isDirektur())
                <select 
                    wire:model.live="filterDepartemen" 
                    class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800"
                >
                    <option value="">Semua Departemen</option>
                    @foreach($departemens as $dept)
                        <option value="{{ $dept->id_departemen }}">{{ $dept->kode_departemen }} - {{ $dept->nama_departemen }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="w-full md:w-64">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari nama atau ID karyawan..." 
                class="w-full h-9 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81]"
            >
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                        <th class="p-3">Karyawan</th>
                        <th class="p-3">Departemen</th>
                        <th class="p-3">Regu / Grup</th>
                        <th class="p-3 text-center">Hadir</th>
                        <th class="p-3 text-center">Terlambat</th>
                        <th class="p-3 text-center">Alpa</th>
                        <th class="p-3 text-center">Izin/Cuti</th>
                        <th class="p-3 text-center">Total Lembur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($karyawans as $emp)
                        @php
                            $s = $statsByEmp[$emp->id_karyawan] ?? [
                                'hadir' => 0, 'terlambat' => 0, 'menit_terlambat' => 0, 'alpa' => 0, 'izin_sakit_cuti' => 0, 'total_lembur_menit' => 0
                            ];
                        @endphp
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3">
                                <div class="font-medium text-slate-900 leading-tight">{{ $emp->nama_karyawan }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $emp->id_karyawan }} &bull; {{ $emp->jabatan?->nama_jabatan }}</div>
                            </td>
                            <td class="p-3 text-slate-700">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700">
                                    {{ $emp->departemen?->kode_departemen }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-600">
                                {{ $emp->grup?->nama_grup ?? 'General Shift' }}
                            </td>
                            <td class="p-3 text-center font-semibold text-emerald-700">
                                {{ $s['hadir'] }} Hari
                            </td>
                            <td class="p-3 text-center">
                                @if($s['terlambat'] > 0)
                                    <span class="font-medium text-amber-700">{{ $s['terlambat'] }}x</span>
                                    <span class="text-[11px] text-slate-400">({{ $s['menit_terlambat'] }} mnt)</span>
                                @else
                                    <span class="text-slate-400">0</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if($s['alpa'] > 0)
                                    <span class="font-semibold text-rose-600">{{ $s['alpa'] }} Hari</span>
                                @else
                                    <span class="text-slate-400">0</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if($s['izin_sakit_cuti'] > 0)
                                    <span class="font-medium text-blue-700">{{ $s['izin_sakit_cuti'] }} Hari</span>
                                @else
                                    <span class="text-slate-400">0</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if($s['total_lembur_menit'] > 0)
                                    <span class="font-semibold text-indigo-700">{{ round($s['total_lembur_menit'] / 60, 1) }} Jam</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">
                                Tidak ada data karyawan atau absensi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($karyawans->hasPages())
            <div class="p-3 border-t border-slate-200">
                {{ $karyawans->links() }}
            </div>
        @endif
    </div>
</div>
