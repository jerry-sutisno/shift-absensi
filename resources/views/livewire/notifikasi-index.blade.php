<div>
    <!-- Top Breadcrumb & Action Hub -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Plant 1 - Karawang Barat</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span>Akun &amp; Sistem</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-semibold">Notifikasi</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1 flex-wrap">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pusat Notifikasi Aplikasi</h1>
                @if ($unreadCount > 0)
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-bold">
                        {{ $unreadCount }} Belum Dibaca
                    </span>
                @endif
            </div>
            <p class="text-sm text-slate-500 max-w-4xl">
                Daftar pengumuman jadwal kerja, status pengajuan izin/cuti/lembur, dan informasi operasional pabrik terkini.
            </p>
        </div>

        @if ($unreadCount > 0)
            <div class="flex items-center gap-2.5 shrink-0">
                <button 
                    wire:click="tandaiSemuaDibaca"
                    type="button" 
                    class="h-10 px-4 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[18px]">done_all</span>
                    <span>Tandai Semua Dibaca</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Filter Bar -->
    <div class="flex items-center gap-2 mb-4">
        <button 
            wire:click="$set('filter', 'SEMUA')"
            type="button" 
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $filter === 'SEMUA' ? 'bg-[#0F4C81] text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}"
        >
            Semua Notifikasi
        </button>
        <button 
            wire:click="$set('filter', 'BELUM_DIBACA')"
            type="button" 
            class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $filter === 'BELUM_DIBACA' ? 'bg-[#0F4C81] text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}"
        >
            Belum Dibaca ({{ $unreadCount }})
        </button>
    </div>

    <!-- Notifications List -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm divide-y divide-slate-100 overflow-hidden">
        @forelse ($notifications as $notif)
            <div class="p-4 md:p-5 flex items-start justify-between gap-4 transition-colors {{ $notif->dibaca_at ? 'bg-white' : 'bg-blue-50/40' }}">
                <div class="flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ $notif->dibaca_at ? 'bg-slate-100 text-slate-500' : 'bg-blue-100 text-[#0F4C81]' }}">
                        <span class="material-symbols-outlined text-[22px]">notifications</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-slate-900 text-sm leading-tight">
                                {{ $notif->judul }}
                            </span>
                            @if (!$notif->dibaca_at)
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-700 mt-1 leading-relaxed">
                            {{ $notif->pesan }}
                        </p>
                        <div class="flex items-center gap-3 mt-2 text-[11px] text-slate-400">
                            <span>{{ $notif->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                            @if ($notif->url)
                                <span>&bull;</span>
                                <a href="{{ $notif->url }}" class="text-[#0F4C81] hover:underline font-semibold flex items-center gap-0.5">
                                    <span>Buka Tautan</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_outward</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                @if (!$notif->dibaca_at)
                    <button 
                        wire:click="tandaiDibaca({{ $notif->id_notifikasi }})"
                        type="button" 
                        title="Tandai dibaca"
                        class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors shrink-0 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[18px]">check</span>
                    </button>
                @endif
            </div>
        @empty
            <div class="p-12 text-center text-slate-500">
                <span class="material-symbols-outlined text-[48px] text-slate-300">notifications_off</span>
                <p class="text-base font-semibold mt-2">Tidak ada notifikasi</p>
                <p class="text-xs text-slate-400 mt-0.5">Semua notifikasi operasional Anda telah ditinjau.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
