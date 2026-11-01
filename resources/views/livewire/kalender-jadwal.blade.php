<div>
    <!-- Top Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Operasional</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-medium">Kalender Jadwal</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1">
                <h1 class="text-xl font-semibold text-slate-900">Kalender Jadwal Shift</h1>
            </div>
            <p class="text-xs text-slate-500 max-w-3xl">
                Matriks distribusi jadwal kerja operator per grup dan tanggal dalam satu bulan kalender.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            @if(Auth::user()->isAdminHr() || Auth::user()->isSupervisor())
                @if($totalDraft > 0)
                    <button 
                        wire:click="publishBulanIni"
                        type="button" 
                        class="h-9 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[18px]">publish</span>
                        <span>Publikasikan Jadwal ({{ $totalDraft }} Draft)</span>
                    </button>
                @endif
            @endif
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="p-4 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Total Karyawan Ditampilkan</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ count($karyawanList) }}</span>
            <span class="text-xs text-slate-500 mt-1">Operator aktif</span>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Jadwal Terpublikasi</span>
            <span class="text-2xl font-semibold text-emerald-600 mt-2">{{ $totalPublished }}</span>
            <span class="text-xs text-slate-500 mt-1">Dapat dilihat operator</span>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Jadwal Draft</span>
            <span class="text-2xl font-semibold text-amber-600 mt-2">{{ $totalDraft }}</span>
            <span class="text-xs text-slate-500 mt-1">Perlu dipublikasikan</span>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Shift OFF / Libur</span>
            <span class="text-2xl font-semibold text-slate-600 mt-2">{{ $totalOff }}</span>
            <span class="text-xs text-slate-500 mt-1">Hari istirahat terjadwal</span>
        </div>
    </div>

    <!-- Filters & Month Navigation -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 mb-6 flex flex-col lg:flex-row items-center justify-between gap-4">
        <!-- Month Selector Controls -->
        <div class="flex items-center gap-2 w-full lg:w-auto justify-between lg:justify-start">
            <button 
                wire:click="prevMonth"
                type="button" 
                class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50"
            >
                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
            </button>
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-slate-900">{{ $carbonMonth->translatedFormat('F Y') }}</span>
                <input 
                    wire:model.live="filterBulan" 
                    type="month" 
                    class="h-8 px-2 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-700"
                >
            </div>
            <button 
                wire:click="nextMonth"
                type="button" 
                class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50"
            >
                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
            </button>
        </div>

        <!-- Filter Controls -->
        <div class="flex items-center gap-2 w-full lg:w-auto flex-wrap">
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

            @if(!Auth::user()->isOperator())
                <select 
                    wire:model.live="filterGrup" 
                    class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800"
                >
                    <option value="">Semua Grup</option>
                    @foreach($grups as $grup)
                        <option value="{{ $grup->id_grup }}">{{ $grup->nama_grup }}</option>
                    @endforeach
                </select>

                <select 
                    wire:model.live="filterShift" 
                    class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800"
                >
                    <option value="">Semua Shift</option>
                    @foreach($shifts as $s)
                        <option value="{{ $s->kode_shift }}">{{ $s->kode_shift }} - {{ $s->nama_shift }}</option>
                    @endforeach
                </select>

                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Cari karyawan..." 
                    class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white w-40"
                >
            @endif
        </div>
    </div>

    <!-- Schedule Matrix Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <th class="p-3 font-semibold sticky left-0 bg-slate-50 z-20 min-w-[180px] border-r border-slate-200">
                            Karyawan / Regu
                        </th>
                        @foreach($daysInMonth as $day)
                            @php
                                $isSunday = $day->isSunday();
                                $isToday = $day->isToday();
                            @endphp
                            <th class="p-2 text-center min-w-[44px] font-medium border-r border-slate-100 {{ $isSunday ? 'bg-rose-50/50 text-rose-600' : '' }} {{ $isToday ? 'bg-blue-50/60 text-[#0F4C81] font-bold' : '' }}">
                                <div>{{ $day->format('d') }}</div>
                                <div class="text-[10px] text-slate-400 uppercase font-normal">{{ substr($day->translatedFormat('D'), 0, 3) }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($karyawanList as $emp)
                        <tr class="hover:bg-slate-50/50">
                            <!-- Employee Sticky Column -->
                            <td class="p-3 sticky left-0 bg-white z-10 border-r border-slate-200 shadow-sm">
                                <div class="font-medium text-slate-900 leading-tight">{{ $emp->nama_karyawan }}</div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $emp->id_karyawan }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $emp->grup?->nama_grup ?? 'Non-Grup' }}</span>
                                </div>
                            </td>

                            <!-- Day Cells -->
                            @foreach($daysInMonth as $day)
                                @php
                                    $cellKey = $emp->id_karyawan . '_' . $day->toDateString();
                                    $j = $scheduleMatrix[$cellKey] ?? null;
                                @endphp
                                <td class="p-1 text-center border-r border-slate-100 align-middle">
                                    @if($j)
                                        <div 
                                            title="{{ $j->shift?->nama_shift }} ({{ $j->status }})"
                                            class="w-8 h-7 mx-auto rounded-lg flex items-center justify-center font-semibold text-[11px] cursor-default
                                                @if($j->kode_shift === 'GS') bg-blue-100 text-blue-800 border border-blue-200
                                                @elseif($j->kode_shift === 'S1') bg-amber-100 text-amber-900 border border-amber-200
                                                @elseif($j->kode_shift === 'S2') bg-orange-100 text-orange-950 border border-orange-200
                                                @elseif($j->kode_shift === 'S3') bg-indigo-100 text-indigo-900 border border-indigo-200
                                                @else bg-slate-100 text-slate-500 border border-slate-200 @endif
                                                {{ $j->status === 'DRAFT' ? 'opacity-60 ring-1 ring-dashed ring-amber-400' : '' }}
                                            "
                                        >
                                            {{ $j->kode_shift }}
                                        </div>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ iterator_count($daysInMonth) + 1 }}" class="p-8 text-center text-slate-400">
                                Tidak ada data jadwal shift untuk kriteria filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Legend / Footer -->
        <div class="p-3.5 border-t border-slate-200 bg-slate-50 flex items-center justify-between flex-wrap gap-3 text-xs text-slate-600">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="font-medium text-slate-700">Keterangan Shift:</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-100 border border-blue-200"></span> GS (08:00-17:00)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-amber-100 border border-amber-200"></span> S1 (07:00-15:00)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-orange-100 border border-orange-200"></span> S2 (15:00-23:00)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-indigo-100 border border-indigo-200"></span> S3 (23:00-07:00)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-slate-100 border border-slate-200"></span> OFF (Libur)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[11px] text-slate-400">Garis putus-putus = Status DRAFT</span>
            </div>
        </div>
    </div>
</div>
