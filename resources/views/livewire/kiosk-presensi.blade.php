<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col justify-between p-4 md:p-8" wire:poll.1000ms>
    <!-- Top Header: Jam Besar, Shift Berjalan, & Navigasi -->
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-800/80">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-[#0F4C81] text-white flex items-center justify-center shadow-lg shadow-[#0F4C81]/30">
                <span class="material-symbols-outlined text-[28px]">precision_manufacturing</span>
            </div>
            <div>
                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                    <span>SIM-Shift DTY Indorama</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-sky-500/10 text-sky-400 border border-sky-500/20 font-mono">KIOSK MODE</span>
                </h1>
                <p class="text-xs text-slate-400">Terminal Presensi Mandiri Divisi DTY PT Indo-Rama Synthetics Tbk</p>
            </div>
        </div>

        <!-- Big Live Digital Clock & Shift Indicator -->
        <div class="flex items-center gap-4 bg-slate-900/90 border border-slate-800 px-5 py-2.5 rounded-2xl shadow-inner">
            <div class="flex flex-col text-right">
                <div class="font-mono text-2xl md:text-3xl font-extrabold tracking-wider text-emerald-400" id="live-clock">
                    {{ $currentTime }}
                </div>
                <div class="text-[11px] text-slate-400 font-medium">
                    {{ $currentDate }}
                </div>
            </div>
            <div class="h-10 w-px bg-slate-800"></div>
            <div class="flex flex-col">
                <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Shift Berjalan</span>
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
            <a href="{{ route('login') }}" class="ml-2 p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors" title="Kembali ke Halaman Login">
                <span class="material-symbols-outlined text-[20px]">exit_to_app</span>
            </a>
        </div>
    </header>

    <!-- Main Content Grid -->
    <main class="my-auto py-6 max-w-6xl w-full mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Input NIK + Keypad PIN (5 Cols) -->
        <div class="lg:col-span-5 bg-slate-900/80 border border-slate-800/80 rounded-3xl p-6 shadow-xl flex flex-col gap-4">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wider">1. Masukkan NIK / ID Karyawan</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-3 text-slate-500 text-[20px]">badge</span>
                    <input 
                        wire:model.live.debounce.250ms="id_karyawan" 
                        type="text" 
                        placeholder="Contoh: EMP-0013" 
                        class="w-full h-12 pl-11 pr-4 bg-slate-950 border border-slate-800 rounded-xl text-sm font-mono font-semibold text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-[#0F4C81] focus:border-transparent uppercase"
                    >
                </div>
                @if($selectedNama)
                    <div class="mt-2 px-3 py-1.5 rounded-lg bg-sky-500/10 border border-sky-500/20 flex items-center justify-between text-xs">
                        <span class="font-semibold text-sky-300">{{ $selectedNama }}</span>
                        <span class="text-slate-400 text-[11px]">{{ $selectedDepartemen }}</span>
                    </div>
                @endif
            </div>

            <!-- PIN Display (6 Bulatan) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-medium text-slate-400 uppercase tracking-wider">2. Masukkan PIN 6 Digit</label>
                    <button wire:click="clearPin" type="button" class="text-[11px] text-rose-400 hover:underline cursor-pointer">Reset PIN</button>
                </div>
                <div class="h-12 bg-slate-950 border border-slate-800 rounded-xl flex items-center justify-center gap-3 px-4 shadow-inner">
                    @for($i = 0; $i < 6; $i++)
                        <div class="w-4 h-4 rounded-full transition-all duration-150 {{ strlen($pin) > $i ? 'bg-emerald-400 shadow-md shadow-emerald-400/50 scale-110' : 'bg-slate-800 border border-slate-700' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Touch Keypad (1 - 9, C, 0, Backspace) -->
            <div class="grid grid-cols-3 gap-2.5 pt-1">
                @foreach(['1','2','3','4','5','6','7','8','9'] as $key)
                    <button 
                        wire:click="pressKey('{{ $key }}')" 
                        type="button" 
                        class="h-13 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono text-xl font-bold transition-all duration-100 active:scale-95 shadow cursor-pointer border border-slate-700/50 flex items-center justify-center"
                    >
                        {{ $key }}
                    </button>
                @endforeach
                <button 
                    wire:click="clearPin" 
                    type="button" 
                    class="h-13 rounded-2xl bg-rose-950/40 hover:bg-rose-900/50 text-rose-300 font-bold text-xs uppercase tracking-wider transition-all duration-100 active:scale-95 border border-rose-800/40 flex items-center justify-center cursor-pointer"
                >
                    Clear
                </button>
                <button 
                    wire:click="pressKey('0')" 
                    type="button" 
                    class="h-13 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-white font-mono text-xl font-bold transition-all duration-100 active:scale-95 shadow cursor-pointer border border-slate-700/50 flex items-center justify-center"
                >
                    0
                </button>
                <button 
                    wire:click="backspacePin" 
                    type="button" 
                    class="h-13 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-amber-400 transition-all duration-100 active:scale-95 border border-slate-700/50 flex items-center justify-center cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[22px]">backspace</span>
                </button>
            </div>
        </div>

        <!-- Right Column: Action Buttons & Instant Feedback Card (7 Cols) -->
        <div class="lg:col-span-7 flex flex-col gap-6 justify-center">

            <!-- Error Banner -->
            @if($errorMessage)
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-start gap-3 shadow-lg">
                    <span class="material-symbols-outlined text-rose-400 shrink-0 mt-0.5">error</span>
                    <div class="flex-1">
                        <strong class="font-semibold block mb-0.5">Presensi Belum Berhasil</strong>
                        <p class="text-xs text-rose-300/90 leading-relaxed">{{ $errorMessage }}</p>
                    </div>
                    <button wire:click="$set('errorMessage', null)" class="text-rose-400 hover:text-white">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            <!-- Confirmation Success Card -->
            @if($konfirmasi)
                <div class="p-6 md:p-8 rounded-3xl bg-slate-900 border-2 border-emerald-500/60 shadow-2xl flex flex-col gap-5 relative overflow-hidden animate-fadeIn">
                    <div class="absolute -right-8 -top-8 w-36 h-36 rounded-full bg-emerald-500/10 blur-2xl pointer-events-none"></div>

                    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[32px]">check_circle</span>
                            </div>
                            <div>
                                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Presensi Berhasil Dicatat</span>
                                <h3 class="text-xl font-bold text-white">{{ $konfirmasi['nama'] }}</h3>
                            </div>
                        </div>
                        <span class="text-xs font-mono text-slate-400">{{ $konfirmasi['id_karyawan'] }}</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 py-1">
                        <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-medium block">Departemen</span>
                            <span class="text-xs font-semibold text-slate-200 mt-1 block truncate">{{ $konfirmasi['departemen'] }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800">
                            <span class="text-[10px] text-slate-400 uppercase font-medium block">Shift Kerja</span>
                            <span class="text-xs font-semibold text-sky-400 mt-1 block">{{ $konfirmasi['kode_shift'] }} ({{ $konfirmasi['jam_shift'] }})</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-950/60 border border-slate-800 col-span-2 sm:col-span-1">
                            <span class="text-[10px] text-slate-400 uppercase font-medium block">Waktu Tercatat</span>
                            <span class="text-xs font-mono font-bold text-emerald-400 mt-1 block">{{ $konfirmasi['waktu'] }}</span>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-950/30 border border-emerald-800/40 flex items-center justify-between">
                        <span class="text-xs text-slate-300">Status Kehadiran:</span>
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 uppercase">
                            {{ $konfirmasi['status'] }}
                        </span>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button 
                            wire:click="resetKiosk" 
                            type="button" 
                            class="h-11 px-6 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-medium text-xs transition-colors cursor-pointer inline-flex items-center gap-2"
                        >
                            <span class="material-symbols-outlined text-[18px]">refresh</span>
                            <span>Absen Karyawan Berikutnya</span>
                        </button>
                    </div>
                </div>
            @else
                <!-- Two Big Punch Buttons: MASUK & PULANG -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Tombol ABSEN MASUK (Green) -->
                    <button 
                        wire:click="prosesAbsenMasuk" 
                        type="button" 
                        class="h-44 rounded-3xl bg-gradient-to-br from-emerald-600 to-teal-700 hover:from-emerald-500 hover:to-teal-600 text-white p-6 flex flex-col justify-between shadow-2xl shadow-emerald-900/40 transition-all duration-150 active:scale-98 cursor-pointer border border-emerald-400/30 group text-left"
                    >
                        <div class="flex items-center justify-between w-full">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-[28px]">login</span>
                            </div>
                            <span class="text-[11px] font-bold tracking-wider uppercase px-2.5 py-1 rounded-full bg-white/15">Masuk Shift</span>
                        </div>
                        <div>
                            <span class="text-2xl md:text-3xl font-extrabold tracking-tight block">ABSEN MASUK</span>
                            <span class="text-xs text-emerald-100/80 mt-1 block font-medium">Validasi tepat waktu &amp; toleransi shift</span>
                        </div>
                    </button>

                    <!-- Tombol ABSEN PULANG (Blue) -->
                    <button 
                        wire:click="prosesAbsenPulang" 
                        type="button" 
                        class="h-44 rounded-3xl bg-gradient-to-br from-[#0F4C81] to-sky-700 hover:from-[#135d9d] hover:to-sky-600 text-white p-6 flex flex-col justify-between shadow-2xl shadow-sky-950/40 transition-all duration-150 active:scale-98 cursor-pointer border border-sky-400/30 group text-left"
                    >
                        <div class="flex items-center justify-between w-full">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined text-[28px]">logout</span>
                            </div>
                            <span class="text-[11px] font-bold tracking-wider uppercase px-2.5 py-1 rounded-full bg-white/15">Pulang Shift</span>
                        </div>
                        <div>
                            <span class="text-2xl md:text-3xl font-extrabold tracking-tight block">ABSEN PULANG</span>
                            <span class="text-xs text-sky-100/80 mt-1 block font-medium">Hitung total jam kerja &amp; selesai shift</span>
                        </div>
                    </button>
                </div>

                <!-- Info Box -->
                <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs text-slate-400 flex items-center gap-3">
                    <span class="material-symbols-outlined text-slate-500 text-[20px]">info</span>
                    <span>Waktu presensi diverifikasi secara otoritatif berdasarkan jam server Asia/Jakarta. Hubungi Supervisor jika terjadi kendala mesin kiosk.</span>
                </div>
            @endif

        </div>
    </main>

    <!-- Bottom Footer: Quick Demo Selector for Presentation -->
    <footer class="pt-4 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-semibold text-slate-400">⚡ Demo Cepat Operator (PIN: 123456):</span>
            @foreach($demoOperators as $demo)
                <button 
                    wire:click="pilihKaryawanDemo('{{ $demo->id_karyawan }}')" 
                    type="button" 
                    class="px-2.5 py-1 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-300 border border-slate-800 text-[11px] transition-colors cursor-pointer"
                >
                    {{ $demo->id_karyawan }} - {{ $demo->nama_karyawan }}
                </button>
            @endforeach
        </div>

        <div class="text-[11px] text-slate-500 shrink-0">
            SIM-Shift DTY Indorama v1.0 &bull; Divisi DTY PT Indo-Rama Synthetics Tbk
        </div>
    </footer>
</div>
