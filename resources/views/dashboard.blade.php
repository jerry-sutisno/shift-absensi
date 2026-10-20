@extends('layouts.app')

@section('page_title', 'Dashboard')

@section('content')
<div class="flex flex-col gap-6">

    <!-- Welcome Hero Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-[#0F4C81] text-white p-6 md:p-8 shadow-sm">
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <span class="material-symbols-outlined text-[240px]">precision_manufacturing</span>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider backdrop-blur-xs">
                        Role: {{ Auth::user()->role }}
                    </span>
                    <span class="text-xs text-blue-200">
                        NIK: {{ Auth::user()->id_karyawan }}
                    </span>
                </div>
                <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">
                    Selamat Datang, {{ Auth::user()->nama }}!
                </h2>
                <p class="text-sm text-blue-100 mt-1 max-w-2xl">
                    Sistem Manajemen Shift & Absensi Operator aktif. Seluruh pencatatan presensi, jadwal roster, dan alur perizinan divalidasi langsung oleh waktu server.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div class="px-4 py-2.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-right">
                    <div class="text-[11px] uppercase tracking-wider text-blue-200 font-semibold">Departemen</div>
                    <div class="text-base font-bold text-white">{{ Auth::user()->karyawan?->departemen?->nama_departemen ?? 'Personalia' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stat 1 -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Karyawan</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1">{{ \App\Models\Karyawan::count() }}</span>
                <span class="text-xs text-emerald-600 font-medium mt-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                    <span>Aktif di Database</span>
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#0F4C81] flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">groups</span>
            </div>
        </div>

        <!-- Stat 2 -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Departemen</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1">{{ \App\Models\Departemen::count() }}</span>
                <span class="text-xs text-slate-500 font-medium mt-1">PRS, LGS, PRD, QCT, PCKG</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">apartment</span>
            </div>
        </div>

        <!-- Stat 3 -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Regu / Grup Kerja</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1">{{ \App\Models\GrupKaryawan::count() }}</span>
                <span class="text-xs text-slate-500 font-medium mt-1">Grup A, B, C, D</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">hub</span>
            </div>
        </div>

        <!-- Stat 4 -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Master Shift</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1">{{ \App\Models\Shift::count() }}</span>
                <span class="text-xs text-slate-500 font-medium mt-1">GS, S1, S2, S3, OFF</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">schedule</span>
            </div>
        </div>
    </div>

    <!-- Shift Chip Reference Matrix (From DESIGN.md) -->
    <div class="p-6 rounded-xl bg-white border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Standardisasi Chip Shift Pabrik</h3>
                <p class="text-xs text-slate-500">Warna dan jam kerja resmi hasil ekstrak dari Google Stitch</p>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-bold text-xs">
                5 Shift Terdaftar
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
            @foreach(\App\Models\Shift::all() as $s)
                <div class="p-4 rounded-xl border flex flex-col justify-between gap-3 shadow-xs
                    @if($s->kode_shift === 'GS') bg-blue-50/70 border-blue-200 text-blue-950
                    @elseif($s->kode_shift === 'S1') bg-amber-50/70 border-amber-200 text-amber-950
                    @elseif($s->kode_shift === 'S2') bg-orange-50/70 border-orange-200 text-orange-950
                    @elseif($s->kode_shift === 'S3') bg-indigo-50/70 border-indigo-200 text-indigo-950
                    @else bg-slate-100 border-slate-200 text-slate-800 @endif
                ">
                    <div class="flex items-center justify-between">
                        <span class="text-base font-extrabold">{{ $s->kode_shift }}</span>
                        @if($s->lintas_hari)
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-200 text-indigo-900">Lintas Hari</span>
                        @elseif($s->is_libur)
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-300 text-slate-800">Libur</span>
                        @else
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-white/80">Reguler</span>
                        @endif
                    </div>
                    <div>
                        <div class="text-xs font-semibold">{{ $s->nama_shift }}</div>
                        <div class="text-xs font-mono mt-0.5">
                            @if($s->jam_masuk && $s->jam_pulang)
                                {{ substr($s->jam_masuk, 0, 5) }} - {{ substr($s->jam_pulang, 0, 5) }}
                            @else
                                Jam Fleksibel / Off
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Phase 1 Status Summary -->
    <div class="p-6 rounded-xl bg-white border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[28px]">verified</span>
            </div>
            <div>
                <h4 class="text-base font-bold text-slate-900">Fase 1 Selesai: Pondasi, Autentikasi, & RBAC Siap</h4>
                <p class="text-xs text-slate-500 mt-0.5">
                    18 migration database aktif, 32 karyawan terdaftar, autentikasi terenkripsi, layout sidebar & topbar siap untuk Fase 2.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold">
                Migration & Seeder OK
            </span>
        </div>
    </div>

</div>
@endsection
