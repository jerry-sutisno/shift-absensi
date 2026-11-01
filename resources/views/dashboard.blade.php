@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-semibold text-slate-900">Dashboard</h1>
            <p class="text-xs text-slate-500 mt-0.5">Ringkasan operasional shift dan data kepegawaian.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium">
                {{ Auth::user()->role }} &bull; {{ Auth::user()->karyawan?->departemen?->nama_departemen ?? 'Personalia' }}
            </span>
        </div>
    </div>

    <!-- Quick Stats Cards (No circular colored icon containers, clean metrics) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat 1 -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Total Karyawan</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ $totalKaryawan }}</span>
            <span class="text-xs text-slate-500 mt-1">Aktif terdaftar di sistem</span>
        </div>

        <!-- Stat 2 -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Departemen</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ $totalDepartemen }}</span>
            <span class="text-xs text-slate-500 mt-1">PRS, LGS, PRD, QCT, PCKG</span>
        </div>

        <!-- Stat 3 -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Regu / Grup Kerja</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ $totalGrup }}</span>
            <span class="text-xs text-slate-500 mt-1">Grup A, B, C, D</span>
        </div>

        <!-- Stat 4 -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Master Shift</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ $totalShift }}</span>
            <span class="text-xs text-slate-500 mt-1">GS, S1, S2, S3, OFF</span>
        </div>
    </div>

    <!-- Shift Reference Matrix -->
    <div class="p-5 rounded-xl bg-white border border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Standardisasi Shift Pabrik</h2>
                <p class="text-xs text-slate-500 mt-0.5">Klasifikasi jam operasional dan acuan jadwal shift.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-medium">
                {{ $totalShift }} Shift
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
            @foreach($shifts as $s)
                <div class="p-3.5 rounded-xl border flex flex-col justify-between gap-3
                    @if($s->kode_shift === 'GS') bg-blue-50/60 border-blue-200 text-blue-900
                    @elseif($s->kode_shift === 'S1') bg-amber-50/60 border-amber-200 text-amber-900
                    @elseif($s->kode_shift === 'S2') bg-orange-50/60 border-orange-200 text-orange-950
                    @elseif($s->kode_shift === 'S3') bg-indigo-50/60 border-indigo-200 text-indigo-950
                    @else bg-slate-50 border-slate-200 text-slate-700 @endif
                ">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold">{{ $s->kode_shift }}</span>
                        @if($s->lintas_hari)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-indigo-100 text-indigo-900 border border-indigo-200">Lintas Hari</span>
                        @elseif($s->is_libur)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-200 text-slate-700">Libur</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-white border border-slate-200 text-slate-700">Reguler</span>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs font-medium">{{ $s->nama_shift }}</div>
                        <div class="text-xs font-mono mt-0.5 text-slate-500">
                            @if($s->jam_masuk && $s->jam_pulang)
                                {{ substr($s->jam_masuk, 0, 5) }} - {{ substr($s->jam_pulang, 0, 5) }}
                            @else
                                Fleksibel / Libur
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Waktu Server -->
    <div class="p-3.5 rounded-xl bg-white border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-600">
        <div>
            <span>Waktu server: Asia/Jakarta</span>
        </div>
        <div class="font-mono text-slate-500">
            {{ now('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
        </div>
    </div>

</div>
@endsection

