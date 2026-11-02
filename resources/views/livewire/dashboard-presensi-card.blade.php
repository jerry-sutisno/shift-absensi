<div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs relative overflow-hidden" wire:poll.3000ms>
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        
        <!-- Identitas Karyawan & Status Kehadiran (Grid Kolom yang Rapi) -->
        <div class="flex flex-col gap-2.5">
            <!-- Baris 1: Nama & Jabatan -->
            <div class="flex items-center gap-2 flex-wrap">
                <h2 class="text-base font-bold text-slate-900 tracking-tight">
                    {{ $karyawan?->nama_karyawan ?? $user->id_karyawan }}
                </h2>
                <span class="font-mono text-xs px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold border border-slate-200/60">
                    {{ $karyawan?->id_karyawan ?? '-' }}
                </span>
                <span class="text-[11px] px-2.5 py-0.5 rounded-full font-bold uppercase tracking-wider
                    @if($user->role === 'ADMIN_HR') bg-purple-50 text-purple-700 border border-purple-200
                    @elseif($user->role === 'DIREKTUR') bg-blue-50 text-blue-800 border border-blue-200
                    @elseif($user->role === 'HOD') bg-amber-50 text-amber-800 border border-amber-200
                    @elseif($user->role === 'SUPERVISOR') bg-teal-50 text-teal-800 border border-teal-200
                    @else bg-sky-50 text-[#0F4C81] border border-sky-200 @endif
                ">
                    {{ $user->role }} &bull; {{ $karyawan?->departemen?->nama_departemen ?? 'General' }}
                </span>
            </div>

            <!-- Baris 2: Detail Badge Terpisah & Rapi -->
            <div class="flex items-center gap-2 flex-wrap text-xs">
                <!-- Badge Tanggal -->
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200/80 text-slate-600">
                    <span class="material-symbols-outlined text-[15px] text-slate-400">calendar_today</span>
                    <span class="font-medium">{{ $now->translatedFormat('l, d F Y') }}</span>
                </div>

                <!-- Badge Shift -->
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200/80 text-slate-700">
                    <span class="material-symbols-outlined text-[15px] text-slate-400">schedule</span>
                    @if($jadwalHariIni && $jadwalHariIni->shift)
                        <span class="font-semibold">{{ $jadwalHariIni->shift->kode_shift }}</span>
                        <span class="text-slate-500 font-mono">({{ substr($jadwalHariIni->shift->jam_masuk, 0, 5) }} - {{ substr($jadwalHariIni->shift->jam_pulang, 0, 5) }})</span>
                    @else
                        <span class="font-medium">General Shift (08:00 - 17:00)</span>
                    @endif
                </div>

                <!-- Badge Status Absensi -->
                @if($absensiHariIni && $absensiHariIni->waktu_pulang)
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        <span>Sudah Pulang ({{ $absensiHariIni->waktu_pulang->format('H:i') }} WIB)</span>
                    </div>
                @elseif($absensiHariIni && $absensiHariIni->waktu_masuk)
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg font-semibold border
                        @if($absensiHariIni->status === 'TERLAMBAT') bg-amber-50 text-amber-800 border-amber-200
                        @else bg-emerald-50 text-emerald-800 border-emerald-200 @endif
                    ">
                        <span class="w-2 h-2 rounded-full @if($absensiHariIni->status === 'TERLAMBAT') bg-amber-500 @else bg-emerald-500 @endif"></span>
                        <span>Masuk {{ $absensiHariIni->waktu_masuk->format('H:i') }} WIB</span>
                        @if($absensiHariIni->status === 'TERLAMBAT')
                            <span class="px-1.5 py-0.2 rounded bg-amber-200/70 text-amber-900 text-[10px] font-bold">
                                Terlambat {{ $absensiHariIni->menit_terlambat }}m
                            </span>
                        @endif
                    </div>
                @else
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 font-semibold border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <span>Belum Absen</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tombol Aksi Absen Masuk & Pulang -->
        <div class="flex items-center gap-2.5 shrink-0 self-start lg:self-center">
            <button 
                wire:click="absenMasuk"
                wire:loading.attr="disabled"
                type="button" 
                class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-all flex items-center gap-1.5 shadow-xs hover:shadow active:scale-95 cursor-pointer disabled:opacity-50"
            >
                <span class="material-symbols-outlined text-[17px]">login</span>
                <span wire:loading.remove wire:target="absenMasuk">Absen Masuk</span>
                <span wire:loading wire:target="absenMasuk">Memproses...</span>
            </button>

            <button 
                wire:click="absenPulang"
                wire:loading.attr="disabled"
                type="button" 
                class="px-4 py-2.5 rounded-xl bg-[#0F4C81] hover:bg-[#0c3c66] text-white font-semibold text-xs transition-all flex items-center gap-1.5 shadow-xs hover:shadow active:scale-95 cursor-pointer disabled:opacity-50"
            >
                <span class="material-symbols-outlined text-[17px]">logout</span>
                <span wire:loading.remove wire:target="absenPulang">Absen Pulang</span>
                <span wire:loading wire:target="absenPulang">Memproses...</span>
            </button>
        </div>

    </div>

    <!-- Alert Notifikasi Feedback (Lebih Rapi & Kompak) -->
    @if($pesanSukses)
        <div class="mt-3 py-2 px-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-[17px]">check_circle</span>
                <span class="font-medium">{{ $pesanSukses }}</span>
            </div>
            <button wire:click="$set('pesanSukses', null)" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                <span class="material-symbols-outlined text-[15px]">close</span>
            </button>
        </div>
    @endif

    @if($pesanError)
        <div class="mt-3 py-2 px-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-rose-600 text-[17px]">error</span>
                <span class="font-medium">{{ $pesanError }}</span>
            </div>
            <button wire:click="$set('pesanError', null)" class="text-rose-500 hover:text-rose-700 cursor-pointer">
                <span class="material-symbols-outlined text-[15px]">close</span>
            </button>
        </div>
    @endif
</div>
