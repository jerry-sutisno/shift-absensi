<div>
    <!-- Top Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Konfigurasi &amp; Master</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-medium">Audit Log</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1">
                <h1 class="text-xl font-semibold text-slate-900">Jejak Audit Aktivitas Sistem</h1>
            </div>
            <p class="text-xs text-slate-500 max-w-3xl">
                Catatan immutable seluruh mutasi jadwal, koreksi absensi manual, approval pengajuan, dan perubahan parameter sistem.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-blue-50 text-[#0F4C81] border border-blue-200 text-xs font-medium flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">security</span>
                <span>Audit Trail Wajib</span>
            </span>
        </div>
    </div>

    <!-- Toolbar Filters -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2.5 w-full md:w-auto flex-wrap">
            <select 
                wire:model.live="filterTabel" 
                class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800"
            >
                <option value="">Semua Tabel</option>
                @foreach($tables as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>

            <select 
                wire:model.live="filterAksi" 
                class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800"
            >
                <option value="">Semua Aksi</option>
                @foreach($actions as $a)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full md:w-64">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Cari user, aksi, ID record..." 
                class="w-full h-9 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81]"
            >
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                        <th class="p-3">Waktu (WIB)</th>
                        <th class="p-3">Pengguna</th>
                        <th class="p-3">Aksi</th>
                        <th class="p-3">Tabel</th>
                        <th class="p-3">ID Record</th>
                        <th class="p-3">IP Address</th>
                        <th class="p-3 text-right">Detail Mutasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3 font-mono text-slate-600 whitespace-nowrap">
                                {{ $log->created_at->format('d-m-Y H:i:s') }}
                            </td>
                            <td class="p-3">
                                @if($log->user)
                                    <div class="font-medium text-slate-900 leading-tight">{{ $log->user->nama }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $log->user->role }}</div>
                                @else
                                    <span class="text-slate-400 font-mono text-[11px]">SYSTEM</span>
                                @endif
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium font-mono
                                    @if(str_contains($log->aksi, 'CREATE') || str_contains($log->aksi, 'SETUJU')) bg-emerald-50 text-emerald-800 border border-emerald-200
                                    @elseif(str_contains($log->aksi, 'UPDATE') || str_contains($log->aksi, 'KOREKSI')) bg-blue-50 text-blue-800 border border-blue-200
                                    @elseif(str_contains($log->aksi, 'DELETE') || str_contains($log->aksi, 'TOLAK')) bg-rose-50 text-rose-800 border border-rose-200
                                    @else bg-slate-100 text-slate-700 @endif
                                ">
                                    {{ $log->aksi }}
                                </span>
                            </td>
                            <td class="p-3 font-mono text-slate-700">
                                {{ $log->tabel }}
                            </td>
                            <td class="p-3 font-mono text-slate-500">
                                {{ $log->id_record }}
                            </td>
                            <td class="p-3 font-mono text-slate-400">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                            <td class="p-3 text-right">
                                <button 
                                    wire:click="showDetail({{ $log->id_log }})"
                                    type="button" 
                                    class="h-7 px-2.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-[11px] font-medium transition-colors cursor-pointer"
                                >
                                    Lihat Data
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                Belum ada data audit log yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-3 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    @if($isDetailModalOpen && $selectedLog)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
            <div class="bg-white rounded-2xl max-w-2xl w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[85vh]">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#0F4C81]">history</span>
                        <div>
                            <h3 class="font-semibold text-sm text-slate-900">Detail Mutasi Audit #{{ $selectedLog->id_log }}</h3>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $selectedLog->aksi }} &bull; {{ $selectedLog->tabel }} (ID: {{ $selectedLog->id_record }})</div>
                        </div>
                    </div>
                    <button wire:click="$set('isDetailModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <div class="p-5 overflow-y-auto flex flex-col gap-4 text-xs">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Data Lama -->
                        <div class="flex flex-col gap-1.5">
                            <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span>Data Lama:</span>
                            </span>
                            <div class="p-3 rounded-xl bg-slate-900 text-slate-200 font-mono text-[11px] overflow-x-auto max-h-72">
                                <pre>{{ json_encode($selectedLog->data_lama, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: 'null' }}</pre>
                            </div>
                        </div>

                        <!-- Data Baru -->
                        <div class="flex flex-col gap-1.5">
                            <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Data Baru:</span>
                            </span>
                            <div class="p-3 rounded-xl bg-slate-900 text-slate-200 font-mono text-[11px] overflow-x-auto max-h-72">
                                <pre>{{ json_encode($selectedLog->data_baru, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: 'null' }}</pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-end">
                    <button 
                        wire:click="$set('isDetailModalOpen', false)" 
                        type="button" 
                        class="h-9 px-4 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-medium cursor-pointer"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
