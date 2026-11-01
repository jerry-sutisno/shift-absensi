<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-between p-3 sm:p-5 lg:p-6 select-none relative overflow-x-hidden" wire:poll.1000ms>
    <!-- Background Gradient Accents -->
    <div class="fixed inset-0 pointer-events-none opacity-40">
        <div class="absolute -top-40 -left-40 w-96 h-96 rounded-full bg-[#0F4C81]/30 blur-3xl"></div>
        <div class="absolute top-1/3 -right-40 w-96 h-96 rounded-full bg-emerald-600/20 blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 rounded-full bg-sky-600/20 blur-3xl"></div>
    </div>

    <!-- Top Header Bar -->
    <header class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-3 pb-4 border-b border-slate-800/80">
        <!-- Brand & Terminal Info -->
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-[#0F4C81] text-white flex items-center justify-center shadow-lg shadow-[#0F4C81]/30 border border-sky-400/20">
                <span class="material-symbols-outlined text-[26px]">precision_manufacturing</span>
            </div>
            <div>
                <h1 class="text-lg md:text-xl font-bold tracking-tight text-white flex items-center gap-2">
                    <span>SIM-Shift DTY Indorama</span>
                    <span class="text-[10px] tracking-wider uppercase px-2 py-0.5 rounded-full bg-sky-500/15 text-sky-300 border border-sky-500/30 font-mono inline-flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>KIOSK MODE</span>
                    </span>
                </h1>
                <p class="text-xs text-slate-400">Terminal Presensi Mandiri Divisi DTY PT Indo-Rama Synthetics Tbk</p>
            </div>
        </div>

        <!-- Right Side: Big Digital Clock & Shift Status -->
        <div class="flex items-center gap-3 bg-slate-900/90 border border-slate-800 px-4 py-2 rounded-2xl shadow-inner self-stretch md:self-auto justify-between md:justify-start">
            <div class="flex flex-col text-right">
                <div class="font-mono text-xl sm:text-2xl font-extrabold tracking-wider text-emerald-400" id="live-clock">
                    {{ $currentTime }}
                </div>
                <div class="text-[11px] text-slate-400 font-medium">
                    {{ $currentDate }}
                </div>
            </div>
            <div class="h-8 w-px bg-slate-800"></div>
            <div class="flex flex-col">
                <span class="text-[9px] text-slate-400 uppercase tracking-wider font-semibold">Shift Berjalan</span>
                @if($currentShift)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-md mt-0.5 text-center
                        @if($currentShift->kode_shift === 'S1') bg-amber-500/20 text-amber-300 border border-amber-500/30
                        @elseif($currentShift->kode_shift === 'S2') bg-orange-500/20 text-orange-300 border border-orange-500/30
                        @elseif($currentShift->kode_shift === 'S3') bg-indigo-500/20 text-indigo-300 border border-indigo-500/30
                        @else bg-blue-500/20 text-blue-300 border border-blue-500/30 @endif
                    ">
                        {{ $currentShift->kode_shift }} - {{ $currentShift->nama_shift }}
                    </span>
                @else
                    <span class="text-xs font-medium text-slate-500">-</span>
                @endif
            </div>

            <!-- Fullscreen & Exit Buttons -->
            <div class="flex items-center gap-1 pl-1 border-l border-slate-800">
                <button 
                    onclick="document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen()" 
                    type="button" 
                    class="p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors cursor-pointer" 
                    title="Layar Penuh (Kiosk Fullscreen)"
                >
                    <span class="material-symbols-outlined text-[18px]">fullscreen</span>
                </button>
                <a 
                    href="{{ route('login') }}" 
                    class="p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors" 
                    title="Kembali ke Halaman Login"
                >
                    <span class="material-symbols-outlined text-[18px]">exit_to_app</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Workspace Grid -->
    <main class="relative z-10 my-auto py-4 max-w-6xl w-full mx-auto grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6 items-stretch">
        
        <!-- Left Column: NIK Input & Tactile Keypad (5 Cols) -->
        <div class="lg:col-span-5 bg-slate-900/90 border border-slate-800/90 rounded-3xl p-5 shadow-2xl backdrop-blur-xl flex flex-col justify-between gap-4">
            <!-- 1. Input NIK -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5 uppercase tracking-wider flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-md bg-[#0F4C81] text-white flex items-center justify-center text-[10px] font-bold">1</span>
                        <span>NIK / ID Karyawan</span>
                    </span>
                    @if($id_karyawan)
                        <button wire:click="resetKiosk" type="button" class="text-[11px] text-slate-400 hover:text-rose-400 flex items-center gap-1 cursor-pointer">
                            <span class="material-symbols-outlined text-[14px]">close</span>
                            <span>Hapus</span>
                        </button>
                    @endif
                </label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-3 text-slate-500 text-[20px]">badge</span>
                    <input 
                        wire:model.live.debounce.250ms="id_karyawan" 
                        type="text" 
                        placeholder="Contoh: EMP-0013" 
                        class="w-full h-11 pl-11 pr-4 bg-slate-950 border border-slate-800 rounded-xl text-sm font-mono font-semibold text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-[#0F4C81] focus:border-transparent uppercase shadow-inner"
                    >
                </div>

                <!-- Verified Operator Mini Card -->
                @if($selectedNama)
                    <div class="mt-2.5 p-2.5 rounded-xl bg-sky-950/40 border border-sky-800/40 flex items-center gap-3 animate-fadeIn">
                        <div class="w-9 h-9 rounded-xl bg-[#0F4C81] text-sky-200 flex items-center justify-center font-bold text-xs font-mono shrink-0">
                            {{ substr($selectedNama, 0, 2) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-bold text-white text-xs truncate">{{ $selectedNama }}</div>
                            <div class="text-[11px] text-slate-400 truncate">
                                {{ $selectedDepartemen ?? 'Divisi DTY' }} &bull; {{ $selectedRegu ?? 'Regu Kerja' }}
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-emerald-400 text-[20px] shrink-0">verified</span>
                    </div>
                @endif
            </div>

            <!-- 2. PIN Display & Indicator Dots -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-semibold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-md bg-[#0F4C81] text-white flex items-center justify-center text-[10px] font-bold">2</span>
                        <span>PIN Keamanan (6 Digit)</span>
                    </label>
                    <button wire:click="clearPin" type="button" class="text-[11px] text-rose-400 hover:text-rose-300 cursor-pointer font-medium">Reset PIN</button>
                </div>
                <div class="h-11 bg-slate-950 border border-slate-800 rounded-xl flex items-center justify-center gap-3.5 px-4 shadow-inner">
                    @for($i = 0; $i < 6; $i++)
                        <div class="w-3.5 h-3.5 rounded-full transition-all duration-150 {{ strlen($pin) > $i ? 'bg-emerald-400 shadow-md shadow-emerald-400/60 scale-125' : 'bg-slate-800 border border-slate-700' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- 3. Tactile Touch Keypad (1 - 9, C, 0, ⌫) -->
            <div class="grid grid-cols-3 gap-2 pt-1">
                @foreach(['1','2','3','4','5','6','7','8','9'] as $key)
                    <button 
                        wire:click="pressKey('{{ $key }}')" 
                        type="button" 
                        class="h-12 rounded-xl bg-slate-800/90 hover:bg-slate-700/90 text-white font-mono text-xl font-bold transition-all duration-100 active:scale-95 shadow cursor-pointer border border-slate-700/60 flex items-center justify-center hover:border-slate-500"
                    >
                        {{ $key }}
                    </button>
                @endforeach
                <button 
                    wire:click="clearPin" 
                    type="button" 
                    class="h-12 rounded-xl bg-rose-950/40 hover:bg-rose-900/60 text-rose-300 font-bold text-xs uppercase tracking-wider transition-all duration-100 active:scale-95 border border-rose-800/40 flex items-center justify-center cursor-pointer hover:border-rose-700"
                >
                    CLEAR
                </button>
                <button 
                    wire:click="pressKey('0')" 
                    type="button" 
                    class="h-12 rounded-xl bg-slate-800/90 hover:bg-slate-700/90 text-white font-mono text-xl font-bold transition-all duration-100 active:scale-95 shadow cursor-pointer border border-slate-700/60 flex items-center justify-center hover:border-slate-500"
                >
                    0
                </button>
                <button 
                    wire:click="backspacePin" 
                    type="button" 
                    class="h-12 rounded-xl bg-slate-800/90 hover:bg-slate-700/90 text-amber-400 transition-all duration-100 active:scale-95 border border-slate-700/60 flex items-center justify-center cursor-pointer hover:border-slate-500"
                    title="Hapus Satu Angka"
                >
                    <span class="material-symbols-outlined text-[20px]">backspace</span>
                </button>
            </div>
        </div>

        <!-- Right Column: Action Buttons & Feedback (7 Cols) -->
        <div class="lg:col-span-7 flex flex-col justify-between gap-4">

            <!-- Error Banner -->
            @if($errorMessage)
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-start gap-3 shadow-lg animate-shake">
                    <span class="material-symbols-outlined text-rose-400 shrink-0 mt-0.5">error</span>
                    <div class="flex-1">
                        <strong class="font-semibold block mb-0.5">Presensi Belum Berhasil</strong>
                        <p class="text-xs text-rose-300/90 leading-relaxed">{{ $errorMessage }}</p>
                    </div>
                    <button wire:click="$set('errorMessage', null)" class="text-rose-400 hover:text-white cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            <!-- Confirmation Success Modal / Card -->
            @if($konfirmasi)
                <div class="p-6 md:p-7 rounded-3xl bg-slate-900 border-2 border-emerald-500/60 shadow-2xl flex flex-col gap-4 relative overflow-hidden animate-fadeIn">
                    <div class="absolute -right-8 -top-8 w-36 h-36 rounded-full bg-emerald-500/15 blur-2xl pointer-events-none"></div>

                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[30px]">check_circle</span>
                            </div>
                            <div>
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-400">Presensi Berhasil Dicatat</span>
                                <h3 class="text-xl font-bold text-white">{{ $konfirmasi['nama'] }}</h3>
                            </div>
                        </div>
                        <span class="text-xs font-mono px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 font-semibold">{{ $konfirmasi['id_karyawan'] }}</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-medium block">Departemen</span>
                            <span class="text-xs font-semibold text-slate-200 mt-0.5 block truncate">{{ $konfirmasi['departemen'] }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-medium block">Shift Kerja</span>
                            <span class="text-xs font-semibold text-sky-400 mt-0.5 block">{{ $konfirmasi['kode_shift'] }} ({{ $konfirmasi['jam_shift'] }})</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800 col-span-2 sm:col-span-1">
                            <span class="text-[10px] text-slate-400 uppercase font-medium block">Waktu Tercatat</span>
                            <span class="text-xs font-mono font-bold text-emerald-400 mt-0.5 block">{{ $konfirmasi['waktu'] }}</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-950/30 border border-emerald-800/40 flex items-center justify-between">
                        <span class="text-xs text-slate-300">Status Kehadiran Sistem:</span>
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 uppercase tracking-wide">
                            {{ $konfirmasi['status'] }}
                        </span>
                    </div>

                    <div class="pt-1 flex justify-end">
                        <button 
                            wire:click="resetKiosk" 
                            type="button" 
                            class="h-11 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition-colors cursor-pointer inline-flex items-center gap-2 shadow-lg shadow-emerald-900/30"
                        >
                            <span class="material-symbols-outlined text-[18px]">refresh</span>
                            <span>Selesai &bull; Absen Karyawan Berikutnya</span>
                        </button>
                    </div>
                </div>
            @else
                <!-- Operator Status Bar (When NIK is entered) -->
                @if($selectedNama)
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center justify-between gap-3 shadow-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-500/10 text-sky-400 border border-sky-500/20 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">account_circle</span>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400">Jadwal Shift Hari Ini:</div>
                                <div class="text-sm font-bold text-white font-mono">{{ $selectedShiftHariIni ?? 'General Shift' }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-400">Status Presensi:</div>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full inline-block mt-0.5
                                @if(str_contains($statusAbsenHariIni ?? '', 'Sudah Masuk')) bg-emerald-500/20 text-emerald-300 border border-emerald-500/30
                                @elseif(str_contains($statusAbsenHariIni ?? '', 'Sudah Pulang')) bg-slate-800 text-slate-300 border border-slate-700
                                @else bg-amber-500/20 text-amber-300 border border-amber-500/30 @endif
                            ">
                                {{ $statusAbsenHariIni ?? 'Belum Absen' }}
                            </span>
                        </div>
                    </div>
                @else
                    <!-- Helpful Guidance Strip -->
                    <div class="p-3.5 rounded-2xl bg-slate-900/80 border border-slate-800/80 text-xs text-slate-400 flex items-center gap-3">
                        <span class="material-symbols-outlined text-sky-400 text-[20px] shrink-0">touch_app</span>
                        <span>Masukkan NIK dan 6 digit PIN di sebelah kiri, lalu sentuh tombol <strong>ABSEN MASUK</strong> atau <strong>ABSEN PULANG</strong>.</span>
                    </div>
                @endif

                <!-- Two Big Punch Buttons: MASUK & PULANG -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Tombol ABSEN MASUK (Green) -->
                    <button 
                        wire:click="prosesAbsenMasuk" 
                        type="button" 
                        class="h-36 sm:h-40 rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-600 to-teal-700 hover:from-emerald-500 hover:to-teal-600 text-white p-5 flex flex-col justify-between shadow-xl shadow-emerald-950/40 transition-all duration-150 active:scale-98 cursor-pointer border border-emerald-400/30 group text-left relative overflow-hidden"
                    >
                        <div class="flex items-center justify-between w-full">
                            <div class="w-11 h-11 rounded-2xl bg-white/15 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-[26px]">login</span>
                            </div>
                            <span class="text-[10px] font-bold tracking-wider uppercase px-2.5 py-1 rounded-full bg-white/20">Masuk Shift</span>
                        </div>
                        <div>
                            <span class="text-2xl font-black tracking-tight block">ABSEN MASUK</span>
                            <span class="text-xs text-emerald-100/80 mt-0.5 block font-medium">Validasi tepat waktu &amp; toleransi shift</span>
                        </div>
                    </button>

                    <!-- Tombol ABSEN PULANG (Blue) -->
                    <button 
                        wire:click="prosesAbsenPulang" 
                        type="button" 
                        class="h-36 sm:h-40 rounded-3xl bg-gradient-to-br from-[#0F4C81] via-[#0F4C81] to-sky-700 hover:from-[#135d9d] hover:to-sky-600 text-white p-5 flex flex-col justify-between shadow-xl shadow-sky-950/40 transition-all duration-150 active:scale-98 cursor-pointer border border-sky-400/30 group text-left relative overflow-hidden"
                    >
                        <div class="flex items-center justify-between w-full">
                            <div class="w-11 h-11 rounded-2xl bg-white/15 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-[26px]">logout</span>
                            </div>
                            <span class="text-[10px] font-bold tracking-wider uppercase px-2.5 py-1 rounded-full bg-white/20">Pulang Shift</span>
                        </div>
                        <div>
                            <span class="text-2xl font-black tracking-tight block">ABSEN PULANG</span>
                            <span class="text-xs text-sky-100/80 mt-0.5 block font-medium">Hitung total jam kerja &amp; selesai shift</span>
                        </div>
                    </button>
                </div>

                <!-- Recent Attendance Stream (Live Log) -->
                <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800/80 flex flex-col gap-2.5 shadow-lg">
                    <div class="flex items-center justify-between text-xs text-slate-400 pb-1.5 border-b border-slate-800">
                        <span class="font-semibold text-slate-300 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>Presensi Terkini Hari Ini</span>
                        </span>
                        <span class="text-[11px] font-mono text-slate-500">Live Server Stream</span>
                    </div>

                    @if(isset($recentAbsensi) && $recentAbsensi->count() > 0)
                        <div class="space-y-1.5">
                            @foreach($recentAbsensi as $rec)
                                <div class="flex items-center justify-between text-xs py-1 px-2 rounded-lg bg-slate-950/60 border border-slate-800/60">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-slate-400 text-[11px] font-semibold">{{ $rec->id_karyawan }}</span>
                                        <span class="font-medium text-slate-200">{{ $rec->karyawan?->nama_karyawan }}</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-800 text-sky-300 font-mono">{{ $rec->jadwal?->shift?->kode_shift ?? 'S1' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-[11px] text-emerald-400">
                                            {{ $rec->waktu_pulang ? Carbon\Carbon::parse($rec->waktu_pulang)->format('H:i') : Carbon\Carbon::parse($rec->waktu_masuk)->format('H:i') }}
                                        </span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $rec->status === 'HADIR' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                                            {{ $rec->waktu_pulang ? 'PULANG' : $rec->status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-2 text-center text-xs text-slate-500 italic">
                            Belum ada presensi tercatat untuk shift ini. Jadilah yang pertama absen!
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </main>

    <!-- Bottom Dock: Quick Demo Operator Bar -->
    <footer class="relative z-10 pt-3 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-2.5 text-xs text-slate-500">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-semibold text-slate-400 text-[11px]">⚡ Demo Cepat Operator (PIN: 123456):</span>
            @foreach($demoOperators as $demo)
                <button 
                    wire:click="pilihKaryawanDemo('{{ $demo->id_karyawan }}')" 
                    type="button" 
                    class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 text-[11px] transition-colors cursor-pointer flex items-center gap-1.5 active:scale-95 hover:border-slate-700"
                >
                    <span class="font-mono font-bold text-sky-400">{{ $demo->id_karyawan }}</span>
                    <span>{{ $demo->nama_karyawan }}</span>
                </button>
            @endforeach
        </div>

        <div class="text-[11px] text-slate-500 shrink-0">
            SIM-Shift DTY Indorama v1.0 &bull; Divisi DTY PT Indo-Rama Synthetics Tbk
        </div>
    </footer>
</div>
