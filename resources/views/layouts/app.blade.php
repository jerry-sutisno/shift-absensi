<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - PlantShift OS</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans text-slate-800 antialiased selection:bg-[#0F4C81] selection:text-white" x-data="{ sidebarOpen: false }">
    <div class="min-h-full flex flex-col lg:flex-row">
        
        <!-- Mobile Sidebar Backdrop -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false" 
            class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden"
            style="display: none;"
        ></div>

        <!-- Sidebar Navigation (Fixed on Desktop) -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 w-72 bg-white border-r border-slate-200 z-50 flex flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)] transition-transform duration-300 ease-in-out"
        >
            <div class="flex flex-col flex-1 overflow-y-auto">
                <!-- Brand Header -->
                <div class="h-16 px-6 flex items-center justify-between border-b border-slate-100 bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#0F4C81] text-white flex items-center justify-center shadow-sm">
                            <span class="material-symbols-outlined text-[24px]">precision_manufacturing</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-bold text-[#0F4C81] text-base tracking-tight leading-tight">PlantShift OS</span>
                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Shift & Absensi</span>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>

                <!-- Navigation Links Per Role -->
                <div class="px-4 py-5 flex flex-col gap-6">
                    @php
                        $user = Auth::user();
                        $role = $user?->role;
                        $currentRoute = Route::currentRouteName();
                    @endphp

                    <!-- Main Section -->
                    <div class="flex flex-col gap-1">
                        <span class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">Menu Utama</span>

                        @if ($user?->isOperator())
                            <a href="{{ route('dashboard') }}" class="h-11 px-3.5 rounded-xl flex items-center gap-3 text-sm font-semibold transition-colors {{ $currentRoute === 'dashboard' ? 'bg-[#0F4C81] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[22px]">home</span>
                                <span>Beranda Operator</span>
                            </a>
                            <a href="#" class="h-11 px-3.5 rounded-xl flex items-center gap-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                <span class="material-symbols-outlined text-[22px]">calendar_month</span>
                                <span>Jadwal Saya</span>
                            </a>
                            <a href="#" class="h-11 px-3.5 rounded-xl flex items-center gap-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                <span class="material-symbols-outlined text-[22px]">fact_check</span>
                                <span>Riwayat Absensi</span>
                            </a>
                            <a href="#" class="h-11 px-3.5 rounded-xl flex items-center gap-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                <span class="material-symbols-outlined text-[22px]">assignment</span>
                                <span>Pengajuan Saya</span>
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="h-11 px-3.5 rounded-xl flex items-center gap-3 text-sm font-semibold transition-colors {{ $currentRoute === 'dashboard' ? 'bg-[#0F4C81] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[22px]">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                            <a href="#" class="h-11 px-3.5 rounded-xl flex items-center gap-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                <span class="material-symbols-outlined text-[22px]">calendar_month</span>
                                <span>Kalender Jadwal</span>
                            </a>
                            <a href="#" class="h-11 px-3.5 rounded-xl flex items-center gap-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                <span class="material-symbols-outlined text-[22px]">fact_check</span>
                                <span>Monitoring Absensi</span>
                            </a>
                            <a href="#" class="h-11 px-3.5 rounded-xl flex items-center gap-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                <span class="material-symbols-outlined text-[22px]">approval</span>
                                <span>Antrean Approval</span>
                            </a>
                            @if ($user?->isSupervisor())
                                <a href="#" class="h-11 px-3.5 rounded-xl flex items-center gap-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                    <span class="material-symbols-outlined text-[22px]">edit_note</span>
                                    <span>Logbook Shift</span>
                                </a>
                            @endif
                            <a href="#" class="h-11 px-3.5 rounded-xl flex items-center gap-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                <span class="material-symbols-outlined text-[22px]">table_chart</span>
                                <span>Rekap Bulanan</span>
                            </a>
                        @endif
                    </div>

                    <!-- Admin / Master Data Section -->
                    @if ($user?->isAdminHr())
                        <div class="flex flex-col gap-1 pt-2 border-t border-slate-100">
                            <span class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">Konfigurasi & Master</span>
                            <a href="{{ route('master.karyawan') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors {{ $currentRoute === 'master.karyawan' ? 'bg-[#0F4C81] text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">badge</span>
                                <span>Data Karyawan</span>
                            </a>
                            <a href="{{ route('pola.rotasi') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors {{ $currentRoute === 'pola.rotasi' ? 'bg-[#0F4C81] text-white font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">sync_alt</span>
                                <span>Pola Rotasi</span>
                            </a>
                            <a href="#" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                <span class="material-symbols-outlined text-[20px]">settings</span>
                                <span>Pengaturan Sistem</span>
                            </a>
                            <a href="#" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                                <span class="material-symbols-outlined text-[20px]">history</span>
                                <span>Audit Log</span>
                            </a>
                        </div>
                    @endif

                    <!-- Terminal Kiosk Link (Always available as quick link) -->
                    <div class="pt-2 border-t border-slate-100">
                        <a href="#" class="p-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 flex items-center justify-between text-xs font-semibold transition-all">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[#0F4C81] text-[20px]">tablet</span>
                                <span>Mode Kiosk Tablet</span>
                            </div>
                            <span class="text-[10px] bg-white px-1.5 py-0.5 rounded border border-slate-200">1280x800</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- User Profile Widget (Sidebar Footer) -->
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-[#0F4C81] text-white flex items-center justify-center font-bold text-sm shrink-0">
                            {{ substr(Auth::user()->nama, 0, 1) }}
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-bold text-sm text-slate-900 truncate leading-tight">{{ Auth::user()->nama }}</span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold 
                                    @if($user?->isAdminHr()) bg-blue-100 text-blue-900
                                    @elseif($user?->isDirektur()) bg-purple-100 text-purple-900
                                    @elseif($user?->isHod()) bg-amber-100 text-amber-900
                                    @elseif($user?->isSupervisor()) bg-orange-100 text-orange-950
                                    @else bg-emerald-100 text-emerald-900 @endif
                                ">
                                    {{ $user?->role }}
                                </span>
                                <span class="text-[11px] text-slate-500 truncate">{{ $user?->karyawan?->departemen?->kode_departemen ?? 'PRS' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar dari sistem" class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 lg:pl-72 flex flex-col min-h-screen">
            <!-- Topbar Header (Matches Stitch Layout) -->
            <header class="h-16 bg-white border-b border-slate-200 px-4 md:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
                <!-- Left: Mobile Menu + Global Search Input -->
                <div class="flex items-center gap-4 flex-1 max-w-md">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <span class="material-symbols-outlined text-[24px]">menu</span>
                    </button>
                    <div class="relative w-full max-w-xs hidden sm:block">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 flex items-center">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                        </span>
                        <input 
                            type="text" 
                            placeholder="Cari NIK, Nama Operator..." 
                            class="w-full h-9 pl-9 pr-3 rounded-lg bg-slate-100/80 border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] transition-all"
                        >
                    </div>
                </div>

                <!-- Center: Operational Plant & Shift Status Chip -->
                <div class="hidden xl:flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100/90 border border-slate-200 text-xs text-slate-700">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <span class="font-semibold text-slate-900">Plant 1 - Karawang</span>
                    <span class="text-slate-400">&bull;</span>
                    <span class="text-slate-600 font-medium">Shift 1 Berjalan (07:00 - 15:00 WIB)</span>
                </div>

                <!-- Right: Clock, Notification, and User Badge -->
                <div class="flex items-center gap-3">
                    <div class="hidden md:flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-xs font-mono text-slate-700">
                        <span class="material-symbols-outlined text-[16px] text-[#0F4C81]">schedule</span>
                        <span id="server-live-clock">{{ now('Asia/Jakarta')->format('H:i:s') }} WIB</span>
                    </div>

                    <!-- Notification with Badge -->
                    <button type="button" class="relative p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-[22px]">notifications</span>
                        <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-rose-500 text-white font-bold text-[10px] flex items-center justify-center">
                            3
                        </span>
                    </button>

                    <!-- Topbar Profile Summary -->
                    <div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
                        <div class="w-8 h-8 rounded-full bg-[#0F4C81] text-white flex items-center justify-center font-bold text-xs shrink-0">
                            {{ substr(Auth::user()->nama, 0, 1) }}
                        </div>
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="font-bold text-xs text-slate-900 leading-tight">{{ Auth::user()->nama }}</span>
                            <span class="text-[11px] text-slate-500 leading-tight">
                                {{ Auth::user()->karyawan?->departemen?->nama_departemen ?? 'Sistem Pabrik' }}
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-4 md:p-8 max-w-7xl w-full mx-auto">
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-xs">
                        <span class="material-symbols-outlined text-emerald-600 text-[22px]">check_circle</span>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3 shadow-xs">
                        <span class="material-symbols-outlined text-rose-600 text-[22px]">error</span>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <!-- Main Footer -->
            <footer class="h-12 border-t border-slate-200 bg-white px-4 md:px-8 flex items-center justify-between text-xs text-slate-500">
                <span>PlantShift OS &bull; Sistem Manajemen Shift & Absensi Operator</span>
                <span>Waktu Server: Asia/Jakarta</span>
            </footer>
        </div>
    </div>

    @livewireScripts
</body>
</html>
