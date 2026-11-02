@extends('layouts.auth')

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col justify-between p-4 md:p-8">
    <!-- Top Bar -->
    <header class="w-full max-w-5xl mx-auto py-3 px-2 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#0F4C81] text-white flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">precision_manufacturing</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold text-[#0F4C81] text-sm leading-tight">SIM-Shift DTY Indorama</span>
                <span class="text-xs text-slate-500">Shift & Presensi Divisi DTY</span>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <div class="flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-slate-200 text-xs text-slate-600 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-medium">Server Terhubung (Asia/Jakarta)</span>
            </div>
        </div>
    </header>

    <!-- Main Card -->
    <main class="w-full max-w-5xl mx-auto my-auto py-6">
        <div class="w-full grid grid-cols-1 lg:grid-cols-12 rounded-xl bg-white border border-slate-200 overflow-hidden">
            
            <!-- Left Panel: Industrial Identity (5 Cols) -->
            <div class="lg:col-span-5 bg-[#0F4C81] text-white p-8 md:p-10 flex flex-col justify-between">
                <div class="flex flex-col gap-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-white text-xs font-medium w-fit border border-white/15">
                        <span class="material-symbols-outlined text-[16px]">factory</span>
                        <span>Sistem Operasional Pabrik</span>
                    </div>

                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight text-white leading-snug">
                            Manajemen Shift & Absensi Operator
                        </h1>
                        <p class="mt-3 text-sm text-blue-100 leading-relaxed">
                            Pengelolaan jadwal kerja regu, pencatatan absensi berbasis waktu server, dan alur persetujuan terintegrasi.
                        </p>
                    </div>

                    <!-- Shift Indicator Badges -->
                    <div class="flex flex-wrap gap-2 pt-2">
                        <span class="px-2.5 py-1 rounded-lg bg-blue-100 text-blue-900 border border-blue-200 text-xs font-semibold">GS &bull; Normal</span>
                        <span class="px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 border border-amber-200 text-xs font-semibold">S1 &bull; Pagi</span>
                        <span class="px-2.5 py-1 rounded-lg bg-orange-100 text-orange-950 border border-orange-200 text-xs font-semibold">S2 &bull; Siang</span>
                        <span class="px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-950 border border-indigo-200 text-xs font-semibold">S3 &bull; Malam</span>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold">OFF &bull; Libur</span>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-white/15 text-xs text-blue-100 flex items-center justify-between">
                    <span>Waktu Server</span>
                    <span class="font-mono">{{ now('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</span>
                </div>
            </div>

            <!-- Right Panel: Form (7 Cols) -->
            <div class="lg:col-span-7 p-8 md:p-10 flex flex-col justify-between">
                <div>
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-slate-900">Masuk</h2>
                        <p class="text-sm text-slate-500 mt-1">
                            Masukkan ID Karyawan dan kata sandi Anda.
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

                    <form action="{{ route('login.post') }}" method="POST" class="flex flex-col gap-4">
                        @csrf

                        <!-- ID Karyawan Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="id_karyawan" class="text-sm font-medium text-slate-700">
                                    ID Karyawan / NIK
                                </label>
                                <span class="text-xs text-slate-400">Contoh: EMP-0001</span>
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
                                    placeholder="Masukkan ID Karyawan" 
                                    class="w-full h-11 pl-10 pr-4 rounded-xl bg-white border border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150"
                                >
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password" class="text-sm font-medium text-slate-700">
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
                                    class="w-full h-11 pl-10 pr-11 rounded-xl bg-white border border-slate-200 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150"
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

                        <!-- Remember -->
                        <div class="flex items-center justify-between text-xs pt-1">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#0F4C81] focus:ring-[#0F4C81] border-slate-300">
                                <span>Ingat saya di perangkat ini</span>
                            </label>
                            <span class="text-slate-400">Bantuan: Bagian Personalia</span>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="mt-2 w-full h-11 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-medium text-sm transition-colors duration-150 flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <span class="material-symbols-outlined text-[20px]">login</span>
                            <span>Masuk</span>
                        </button>
                    </form>
                </div>

                <!-- Quick Test Login Helper -->
                <div class="mt-8 pt-6 border-t border-slate-100">
                    <p class="text-xs font-medium text-slate-500 mb-2">
                        Akun uji coba (kata sandi: <code class="text-slate-700 font-mono">password</code>)
                    </p>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-1.5 text-xs">
                        <button type="button" onclick="setDemo('EMP-0001')" class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-center transition-colors duration-150" title="Subani (Admin HR)">
                            <span class="font-medium block">Admin HR</span>
                            <span class="text-[11px] text-slate-500 font-mono">EMP-0001</span>
                        </button>
                        <button type="button" onclick="setDemo('EMP-0002')" class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-center transition-colors duration-150" title="Jerry (Direktur)">
                            <span class="font-medium block">Direktur</span>
                            <span class="text-[11px] text-slate-500 font-mono">EMP-0002</span>
                        </button>
                        <button type="button" onclick="setDemo('EMP-0004')" class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-center transition-colors duration-150" title="Satrio (HOD PRD)">
                            <span class="font-medium block">HOD PRD</span>
                            <span class="text-[11px] text-slate-500 font-mono">EMP-0004</span>
                        </button>
                        <button type="button" onclick="setDemo('EMP-0009')" class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-center transition-colors duration-150" title="Gilang (Spv PRD)">
                            <span class="font-medium block">Spv PRD</span>
                            <span class="text-[11px] text-slate-500 font-mono">EMP-0009</span>
                        </button>
                        <button type="button" onclick="setDemo('EMP-0013')" class="p-2 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 text-center transition-colors duration-150" title="Sarwan (Operator)">
                            <span class="font-medium block">Operator</span>
                            <span class="text-[11px] text-slate-500 font-mono">EMP-0013</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="w-full max-w-5xl mx-auto py-3 px-2 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} Sistem Manajemen Shift & Absensi Operator
    </footer>
</div>

<script>
    function setDemo(id) {
        document.getElementById('id_karyawan').value = id;
        document.getElementById('password').value = 'password';
    }
</script>
@endsection

