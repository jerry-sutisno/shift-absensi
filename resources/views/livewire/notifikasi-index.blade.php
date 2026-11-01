<div>
    <!-- Top Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Akun &amp; sistem</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-medium">Notifikasi</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1">
                <h1 class="text-xl font-semibold text-slate-900">Pusat notifikasi</h1>
                @if ($unreadCount > 0)
                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-800 border border-rose-200 text-xs font-medium">
                        {{ $unreadCount }} belum dibaca
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 max-w-3xl">
                Pengumuman jadwal kerja, status pengajuan izin, cuti, lembur, dan informasi operasional pabrik.
            </p>
        </div>

        @if ($unreadCount > 0)
            <div class="flex items-center gap-2.5 shrink-0">
                <button 
                    wire:click="tandaiSemuaDibaca"
                    type="button" 
                    class="h-9 px-3.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
                >
                    <span class="material-symbols-outlined text-[16px]">done_all</span>
                    <span>Tandai semua dibaca</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Filter Bar -->
    <div class="flex items-center gap-2 mb-4">
        <button 
            wire:click="$set('filter', 'SEMUA')"
            type="button" 
            class="px-3.5 py-1.5 rounded-xl text-xs font-medium transition-colors duration-150 {{ $filter === 'SEMUA' ? 'bg-[#0F4C81] text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}"
        >
            Semua notifikasi
        </button>
        <button 
            wire:click="$set('filter', 'BELUM_DIBACA')"
            type="button" 
            class="px-3.5 py-1.5 rounded-xl text-xs font-medium transition-colors duration-150 {{ $filter === 'BELUM_DIBACA' ? 'bg-[#0F4C81] text-white' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}"
        >
            Belum dibaca ({{ $unreadCount }})
        </button>
    </div>

    <!-- Notifications List -->
    <div class="rounded-xl border border-slate-200 bg-white divide-y divide-slate-100 overflow-hidden">
        @forelse ($notifications as $notif)
            <div class="p-4 flex items-start justify-between gap-4 transition-colors duration-150 {{ $notif->dibaca_at ? 'bg-white' : 'bg-sky-50/30' }}">
                <div class="flex items-start gap-3">
                    <div class="mt-1">
                        @if (!$notif->dibaca_at)
                            <span class="w-2 h-2 rounded-full bg-[#0F4C81] block"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-slate-300 block"></span>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-medium text-slate-900 text-xs leading-tight">
                                {{ $notif->judul }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            {{ $notif->pesan }}
                        </p>
                        <div class="flex items-center gap-2.5 mt-2 text-[11px] text-slate-400">
                            <span>{{ $notif->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                            @if ($notif->url)
                                <span>&bull;</span>
                                <a href="{{ $notif->url }}" class="text-[#0F4C81] hover:underline font-medium inline-flex items-center gap-0.5">
                                    <span>Buka tautan</span>
                                    <span class="material-symbols-outlined text-[13px]">arrow_outward</span>
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
                        class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors duration-150 shrink-0 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[18px]">check</span>
                    </button>
                @endif
            </div>
        @empty
            <div class="p-10 text-center text-slate-500 text-xs">
                Tidak ada notifikasi untuk ditampilkan.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>
