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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

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
            x-transition:enter="transition-opacity ease-linear duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false" 
            class="fixed inset-0 bg-slate-900/40 z-40 lg:hidden"
            style="display: none;"
        ></div>

        <!-- Sidebar Navigation (Fixed on Desktop) -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed inset-y-0 left-0 w-72 bg-white border-r border-slate-200 z-50 flex flex-col justify-between transition-transform duration-200 ease-in-out"
        >
            <div class="flex flex-col flex-1 overflow-y-auto">
                <!-- Brand Header -->
                <div class="h-16 px-6 flex items-center justify-between border-b border-slate-100 bg-white">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#0F4C81] text-white flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">precision_manufacturing</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-semibold text-[#0F4C81] text-base leading-tight">PlantShift OS</span>
                            <span class="text-xs text-slate-500">Shift &amp; Absensi</span>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <!-- Navigation Links Per Role -->
                <div class="px-4 py-4 flex flex-col gap-5">
                    @php
                        $user = Auth::user();
                        $role = $user?->role;
                        $currentRoute = Route::currentRouteName();
                    @endphp

                    <!-- Main Section -->
                    <div class="flex flex-col gap-1">
                        <span class="px-3 pb-1.5 text-xs font-medium text-slate-400">Menu Utama</span>

                        @if ($user?->isOperator())
                            <a href="{{ route('dashboard') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'dashboard' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">home</span>
                                <span>Beranda Operator</span>
                            </a>
                            <a href="{{ route('jadwal.kalender') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'jadwal.kalender' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                                <span>Jadwal Saya</span>
                            </a>
                            <a href="{{ route('absensi.koreksi') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'absensi.koreksi' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">fact_check</span>
                                <span>Riwayat Absensi</span>
                            </a>
                            <a href="{{ route('pengajuan.approval') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'pengajuan.approval' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">assignment</span>
                                <span>Pengajuan &amp; Tukar Shift</span>
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'dashboard' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('jadwal.kalender') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'jadwal.kalender' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                                <span>Kalender Jadwal</span>
                            </a>
                            <a href="{{ route('absensi.koreksi') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'absensi.koreksi' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">fact_check</span>
                                <span>Monitoring Absensi</span>
                            </a>
                            <a href="{{ route('pengajuan.approval') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'pengajuan.approval' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">approval</span>
                                <span>Antrean Approval</span>
                            </a>
                            @if ($user?->isSupervisor() || $user?->isAdminHr() || $user?->isHod())
                                <a href="{{ route('logbook') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'logbook' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <span class="material-symbols-outlined text-[20px]">edit_note</span>
                                    <span>Logbook Shift</span>
                                </a>
                            @endif
                            <a href="{{ route('laporan.rekap') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'laporan.rekap' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">table_chart</span>
                                <span>Rekap Bulanan</span>
                            </a>
                        @endif
                    </div>

                    <!-- Admin / Master Data Section -->
                    @if ($user?->isAdminHr())
                        <div class="flex flex-col gap-1 pt-3 border-t border-slate-100">
                            <span class="px-3 pb-1.5 text-xs font-medium text-slate-400">Konfigurasi &amp; Master</span>
                            <a href="{{ route('master.karyawan') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'master.karyawan' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">badge</span>
                                <span>Data Karyawan</span>
                            </a>
                            <a href="{{ route('pola.rotasi') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'pola.rotasi' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">sync_alt</span>
                                <span>Pola Rotasi</span>
                            </a>
                            <a href="{{ route('hari.libur') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'hari.libur' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">calendar_today</span>
                                <span>Hari Libur</span>
                            </a>
                            <a href="{{ route('pengaturan.sistem') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'pengaturan.sistem' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">settings</span>
                                <span>Pengaturan Sistem</span>
                            </a>
                            <a href="{{ route('audit.log') }}" class="h-10 px-3.5 rounded-xl flex items-center gap-3 text-sm font-medium transition-colors duration-150 {{ $currentRoute === 'audit.log' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                <span class="material-symbols-outlined text-[20px]">history</span>
                                <span>Audit Log</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Profile Widget (Sidebar Footer) -->
            <div class="p-3.5 border-t border-slate-200 bg-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-full bg-[#0F4C81] text-white flex items-center justify-center font-medium text-xs shrink-0">
                            {{ substr(Auth::user()->nama, 0, 1) }}
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-medium text-xs text-slate-900 truncate leading-tight">{{ Auth::user()->nama }}</span>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-700">
                                    {{ $user?->role }}
                                </span>
                                <span class="text-[11px] text-slate-400 truncate">{{ $user?->karyawan?->departemen?->kode_departemen ?? 'PRS' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar dari sistem" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors duration-150 cursor-pointer">
                            <span class="material-symbols-outlined text-[20px]">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 lg:pl-72 flex flex-col min-h-screen">
            <!-- Topbar Header -->
            <header class="h-16 bg-white border-b border-slate-200 px-4 md:px-8 flex items-center justify-between sticky top-0 z-30">
                <!-- Left: Mobile Menu -->
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <span class="material-symbols-outlined text-[22px]">menu</span>
                    </button>
                </div>

                <!-- Center: Operational Shift Status -->
                <div class="hidden xl:flex items-center gap-2 px-3 py-1 rounded-full bg-slate-50 border border-slate-200 text-xs text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="font-medium text-slate-800">Shift 1 (07:00 - 15:00 WIB)</span>
                </div>

                <!-- Right: Clock, Notification, and User Badge -->
                <div class="flex items-center gap-3">
                    <div class="hidden md:flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-xs font-mono text-slate-600">
                        <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span>
                        <span id="server-live-clock">{{ now('Asia/Jakarta')->format('H:i:s') }} WIB</span>
                    </div>

                    <!-- Notification -->
                    @php
                        $unreadNotifs = \App\Services\NotificationService::unreadCount(Auth::id());
                    @endphp
                    <a href="{{ route('notifikasi') }}" title="Notifikasi Sistem" class="relative p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-50 transition-colors duration-150">
                        <span class="material-symbols-outlined text-[20px]">notifications</span>
                        @if ($unreadNotifs > 0)
                            <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 rounded-full bg-rose-600 text-white font-medium text-[10px] flex items-center justify-center">
                                {{ $unreadNotifs > 99 ? '99+' : $unreadNotifs }}
                            </span>
                        @endif
                    </a>

                    <!-- Topbar Profile Summary -->
                    <div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
                        <div class="w-8 h-8 rounded-full bg-[#0F4C81] text-white flex items-center justify-center font-medium text-xs shrink-0">
                            {{ substr(Auth::user()->nama, 0, 1) }}
                        </div>
                        <div class="hidden sm:flex flex-col text-left">
                            <span class="font-medium text-xs text-slate-900 leading-tight">{{ Auth::user()->nama }}</span>
                            <span class="text-[11px] text-slate-400 leading-tight">
                                {{ Auth::user()->karyawan?->departemen?->nama_departemen ?? 'Sistem Pabrik' }}
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Body -->
            <main class="flex-1 p-4 md:p-6 max-w-7xl w-full mx-auto">
                @if (session('success'))
                    <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')
            </main>

            <!-- Main Footer -->
            <footer class="h-12 border-t border-slate-200 bg-white px-4 md:px-8 flex items-center justify-between text-xs text-slate-400">
                <span>PlantShift OS &bull; Sistem Manajemen Shift &amp; Absensi Operator</span>
                <span>Waktu Server: Asia/Jakarta</span>
            </footer>
        </div>
    </div>

    @livewireScripts
</body>
</html>

