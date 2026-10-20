@extends('layouts.auth')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] flex flex-col justify-between p-4 md:p-8">
    <!-- Top Bar -->
    <header class="w-full max-w-6xl mx-auto py-3 px-2 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#0F4C81] text-white flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-[24px]">precision_manufacturing</span>
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-[#0F4C81] text-base tracking-tight leading-tight">PlantShift OS</span>
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Shift & Absensi Operator</span>
            </div>
        </div>

        <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-slate-200 shadow-sm">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="text-xs font-medium text-slate-600">Sistem Aktif &bull; Server Terhubung</span>
        </div>
    </header>

    <!-- Main Container -->
    <main class="w-full max-w-6xl mx-auto my-auto py-6">
        <div class="w-full grid grid-cols-1 lg:grid-cols-12 rounded-2xl overflow-hidden shadow-xl bg-white border border-slate-200">
            
            <!-- Left Panel: Industrial Identity (5 Cols) -->
            <div class="lg:col-span-5 relative bg-[#0F4C81] text-white p-8 md:p-10 flex flex-col justify-between overflow-hidden">
                <!-- Background Pattern Vector Overlay -->
                <div class="absolute inset-0 pointer-events-none opacity-10">
                    <svg class="w-full h-full object-cover" fill="none" viewBox="0 0 400 600" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="grid-pattern" width="32" height="32" patternUnits="userSpaceOnUse">
                                <path d="M 32 0 L 0 0 0 32" fill="none" stroke="currentColor" stroke-width="1" />
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#grid-pattern)" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col gap-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white/90 text-xs font-semibold backdrop-blur-md w-fit border border-white/10">
                        <span class="material-symbols-outlined text-[16px]">factory</span>
                        <span>Plant Manufacturing Suite</span>
                    </div>

                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-white leading-tight">
                            Manajemen Shift & Kehadiran Operator Pabrik
                        </h1>
                        <p class="mt-3 text-sm text-blue-100/90 leading-relaxed">
                            Aplikasi operasional terintegrasi untuk penjadwalan rotasi regu kerja, validasi presensi waktu server, dan approval berjenjang.
                        </p>
                    </div>

                    <!-- Shift Indicator Badges -->
                    <div class="flex flex-wrap gap-2 pt-2">
                        <span class="px-2.5 py-1 rounded-lg bg-blue-500/20 text-blue-200 border border-blue-400/30 text-xs font-bold">GS • Normal</span>
                        <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-200 border border-amber-400/30 text-xs font-bold">S1 • Pagi</span>
                        <span class="px-2.5 py-1 rounded-lg bg-orange-500/20 text-orange-200 border border-orange-400/30 text-xs font-bold">S2 • Siang</span>
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-500/20 text-indigo-200 border border-indigo-400/30 text-xs font-bold">S3 • Malam</span>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-500/20 text-slate-200 border border-slate-400/30 text-xs font-bold">OFF • Libur</span>
                    </div>
                </div>

                <!-- Left Panel Footer -->
                <div class="relative z-10 mt-8 pt-6 border-t border-white/10 flex flex-col gap-3">
                    <div class="flex items-center justify-between text-xs text-blue-200">
                        <span>Zona Waktu: Asia/Jakarta (WIB)</span>
                        <span class="font-mono">{{ now('Asia/Jakarta')->format('d-m-Y H:i') }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-black/20 text-xs text-blue-100 flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-amber-400 text-[18px]">verified_user</span>
                        <span>Autentikasi terenkripsi bcrypt & validasi role ketat.</span>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Authentication Form Console (7 Cols) -->
            <div class="lg:col-span-7 bg-white p-8 md:p-10 flex flex-col justify-between">
                <div>
                    <!-- Header Status Pill -->
                    <div class="flex items-center justify-between pb-4 mb-6 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[#0F4C81] text-white flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-[20px]">domain</span>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 text-sm">PlantShift OS</span>
                                    <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-900 font-bold text-[10px]">PROD</span>
                                </div>
                                <span class="text-xs text-slate-500">Plant 1 - Manufaktur</span>
                            </div>
                        </div>

                        <div class="hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-slate-200 text-xs text-slate-700">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Terminal Siap</span>
                        </div>
                    </div>

                    <!-- Headings -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Masuk ke Sistem</h2>
                        <p class="text-sm text-slate-500 mt-1">
                            Masukkan ID Karyawan dan kata sandi Anda untuk mengakses jadwal dan menu operasional.
                        </p>
                    </div>

                    @if (session('success'))
                        <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start gap-2">
                            <span class="material-symbols-outlined text-rose-600 text-[20px] mt-0.5">error</span>
                            <div class="flex flex-col">
                                @foreach ($errors->all() as $error)
                                    <span>{{ $error }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Form -->
                    <form action="{{ route('login.post') }}" method="POST" class="flex flex-col gap-4">
                        @csrf

                        <!-- ID Karyawan Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="id_karyawan" class="text-sm font-semibold text-slate-800">
                                    ID Karyawan / NIK
                                </label>
                                <span class="text-xs text-slate-500">Contoh: EMP-0001</span>
                            </div>
                            <div class="relative flex items-center">
                                <div class="absolute left-3.5 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">badge</span>
                                </div>
                                <input 
                                    type="text" 
                                    name="id_karyawan" 
                                    id="id_karyawan" 
                                    value="{{ old('id_karyawan') }}" 
                                    required 
                                    autofocus
                                    placeholder="Masukkan ID Karyawan..." 
                                    class="w-full h-11 pl-10 pr-4 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81] focus:border-transparent transition-all"
                                >
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password" class="text-sm font-semibold text-slate-800">
                                    Kata Sandi
                                </label>
                            </div>
                            <div class="relative flex items-center" x-data="{ show: false }">
                                <div class="absolute left-3.5 flex items-center pointer-events-none text-slate-400">
                                    <span class="material-symbols-outlined text-[20px]">lock</span>
                                </div>
                                <input 
                                    :type="show ? 'text' : 'password'" 
                                    name="password" 
                                    id="password" 
                                    required 
                                    placeholder="••••••••" 
                                    class="w-full h-11 pl-10 pr-11 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81] focus:border-transparent transition-all"
                                >
                                <button 
                                    type="button" 
                                    @click="show = !show" 
                                    class="absolute right-3 text-slate-400 hover:text-slate-600 focus:outline-none"
                                >
                                    <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Remember & Help -->
                        <div class="flex items-center justify-between text-xs pt-1">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#0F4C81] focus:ring-[#0F4C81] border-slate-300">
                                <span>Ingat saya di perangkat ini</span>
                            </label>
                            <span class="text-slate-500">Bantuan: Personalia (PRS)</span>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="mt-2 w-full h-11 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-semibold text-sm shadow-md hover:shadow-lg active:scale-[0.99] transition-all flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-[20px]">login</span>
                            <span>Masuk ke Sistem</span>
                        </button>
                    </form>
                </div>

                <!-- Quick Test Login Helper Accordion -->
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                        Akun Uji Coba Cepat (Password: <code class="bg-slate-100 px-1 py-0.5 rounded text-[#0F4C81]">password</code>)
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-1.5 text-[11px]">
                        <button type="button" onclick="setDemo('EMP-0001')" class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-900 font-bold border border-blue-200 text-center transition-colors">
                            Admin HR<br><span class="font-normal text-[10px] text-blue-700">EMP-0001</span>
                        </button>
                        <button type="button" onclick="setDemo('EMP-0002')" class="p-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-900 font-bold border border-purple-200 text-center transition-colors">
                            Direktur<br><span class="font-normal text-[10px] text-purple-700">EMP-0002</span>
                        </button>
                        <button type="button" onclick="setDemo('EMP-0004')" class="p-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold border border-amber-200 text-center transition-colors">
                            HOD PRD<br><span class="font-normal text-[10px] text-amber-700">EMP-0004</span>
                        </button>
                        <button type="button" onclick="setDemo('EMP-0009')" class="p-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-900 font-bold border border-orange-200 text-center transition-colors">
                            Spv PRD<br><span class="font-normal text-[10px] text-orange-700">EMP-0009</span>
                        </button>
                        <button type="button" onclick="setDemo('EMP-0013')" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-bold border border-emerald-200 text-center transition-colors">
                            Operator<br><span class="font-normal text-[10px] text-emerald-700">EMP-0013</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-6xl mx-auto py-3 px-2 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Sistem Manajemen Shift & Absensi Operator. Selaras dengan standar industri manufaktur.
    </footer>
</div>

<script>
    function setDemo(id) {
        document.getElementById('id_karyawan').value = id;
        document.getElementById('password').value = 'password';
    }
</script>
@endsection
