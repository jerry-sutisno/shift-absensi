<div>
    <!-- Top Breadcrumb & Action Hub -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Plant 1 - Karawang Barat</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span>Operasional</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-semibold">Logbook Shift</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1 flex-wrap">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Logbook Serah Terima Shift</h1>
                <span class="px-2.5 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-bold tracking-wider uppercase">
                    Handover Operasional Pabrik
                </span>
            </div>
            <p class="text-sm text-slate-500 max-w-4xl">
                Catat ringkasan output produksi, kendala mesin/material, dan instruksi tindak lanjut untuk supervisor dan regu shift berikutnya.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <button 
                wire:click="openCreateModal"
                type="button" 
                class="h-10 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-semibold text-xs shadow-md hover:shadow-lg active:scale-[0.98] transition-all flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">post_add</span>
                <span>+ Buat Catatan Serah Terima</span>
            </button>
        </div>
    </div>

    <!-- Toolbar Filters -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto flex-wrap">
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-bold text-slate-600">Tanggal:</span>
                <input 
                    wire:model.live="filterTanggal" 
                    type="date" 
                    class="h-10 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
                >
            </div>

            <select 
                wire:model.live="filterDepartemen" 
                class="h-10 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
            >
                <option value="">Semua Departemen</option>
                @foreach ($departemens as $d)
                    <option value="{{ $d->id_departemen }}">{{ $d->nama_departemen }}</option>
                @endforeach
            </select>

            <select 
                wire:model.live="filterShift" 
                class="h-10 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
            >
                <option value="">Semua Shift</option>
                @foreach ($shifts as $s)
                    <option value="{{ $s->kode_shift }}">{{ $s->kode_shift }} ({{ $s->nama_shift }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Logbook Cards List -->
    <div class="flex flex-col gap-4">
        @forelse ($logbooks as $log)
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5 md:p-6 flex flex-col gap-4">
                <!-- Header Card -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="px-3 py-1 rounded-lg text-xs font-extrabold
                            @if($log->kode_shift === 'S1') bg-amber-100 text-amber-900 border border-amber-300
                            @elseif($log->kode_shift === 'S2') bg-orange-100 text-orange-950 border border-orange-300
                            @elseif($log->kode_shift === 'S3') bg-indigo-100 text-indigo-950 border border-indigo-300
                            @else bg-blue-100 text-blue-900 border border-blue-300 @endif
                        ">
                            Shift {{ $log->kode_shift }} &bull; {{ $log->shift->nama_shift }}
                        </span>
                        <span class="font-bold text-slate-900 text-sm">
                            {{ $log->departemen->nama_departemen }}
                            @if ($log->grup) &bull; <span class="text-blue-600 font-semibold">{{ $log->grup->nama_grup }}</span> @endif
                        </span>
                        <span class="text-xs text-slate-500 font-medium">
                            ({{ $log->tanggal->format('d M Y') }})
                        </span>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span class="material-symbols-outlined text-[16px] text-[#0F4C81]">person</span>
                        <span>Dibuat oleh: <b class="text-slate-800">{{ $log->pembuat->nama }}</b></span>
                        <span class="text-slate-300">&bull;</span>
                        <span>{{ $log->created_at->format('H:i') }} WIB</span>
                    </div>
                </div>

                <!-- 3 Structured Log Sections -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <!-- Ringkasan Pekerjaan -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-1.5">
                        <span class="font-bold text-slate-900 flex items-center gap-1.5 text-emerald-700">
                            <span class="material-symbols-outlined text-[18px]">task_alt</span>
                            <span>Ringkasan Pekerjaan Selesai</span>
                        </span>
                        <p class="text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $log->ringkasan }}
                        </p>
                    </div>

                    <!-- Kendala & Masalah -->
                    <div class="p-3.5 rounded-xl bg-amber-50/60 border border-amber-200 flex flex-col gap-1.5">
                        <span class="font-bold text-amber-900 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">report_problem</span>
                            <span>Kendala / Problem Mesin &amp; Bahan</span>
                        </span>
                        <p class="text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $log->kendala ?: 'Tidak ada kendala operasional yang dilaporkan.' }}
                        </p>
                    </div>

                    <!-- Tindak Lanjut -->
                    <div class="p-3.5 rounded-xl bg-blue-50/60 border border-blue-200 flex flex-col gap-1.5">
                        <span class="font-bold text-blue-900 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            <span>Tindak Lanjut Shift Berikutnya</span>
                        </span>
                        <p class="text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $log->tindak_lanjut ?: 'Lanjutkan pekerjaan sesuai target SOP harian.' }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 rounded-xl bg-white border border-slate-200 text-center text-slate-500">
                <span class="material-symbols-outlined text-[48px] text-slate-300">menu_book</span>
                <p class="text-base font-semibold mt-2">Belum ada catatan logbook untuk tanggal ini</p>
                <p class="text-xs text-slate-400 mt-0.5">Supervisor dapat menekan tombol "+ Buat Catatan Serah Terima" untuk memulai.</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $logbooks->links() }}
        </div>
    </div>

    <!-- MODAL BUAT LOGBOOK -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            <div class="w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
                <div class="h-16 px-6 border-b border-slate-200 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0F4C81] text-[24px]">post_add</span>
                        <h3 class="font-bold text-slate-900 text-base">Buat Logbook Serah Terima Shift</h3>
                    </div>
                    <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="simpanLogbook" class="p-6 flex flex-col gap-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-slate-700">Tanggal Shift *</label>
                            <input 
                                wire:model="tanggal" 
                                type="date" 
                                class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]"
                            >
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-slate-700">Shift *</label>
                            <select wire:model="kode_shift" class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]">
                                @foreach ($shifts as $s)
                                    <option value="{{ $s->kode_shift }}">{{ $s->kode_shift }} ({{ $s->nama_shift }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-slate-700">Departemen *</label>
                            <select wire:model="id_departemen" class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]">
                                @foreach ($departemens as $d)
                                    <option value="{{ $d->id_departemen }}">{{ $d->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-slate-700">Regu / Grup Kerja (Opsional)</label>
                            <select wire:model="id_grup" class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]">
                                <option value="">-- Tanpa Regu Khusus --</option>
                                @foreach ($grups as $g)
                                    <option value="{{ $g->id_grup }}">{{ $g->nama_grup }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Ringkasan -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700">1. Ringkasan Pekerjaan Selesai *</label>
                        <textarea 
                            wire:model="ringkasan" 
                            rows="3" 
                            placeholder="Tuliskan output lini, target kuota yang tercapai, dan aktivitas penting..." 
                            class="p-3 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-[#0F4C81]"
                        ></textarea>
                        @error('ringkasan') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kendala -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700">2. Kendala / Masalah Mesin / Bahan (Jika ada)</label>
                        <textarea 
                            wire:model="kendala" 
                            rows="2" 
                            placeholder="Tuliskan downtime mesin, bahan defect, atau kekurangan manpower..." 
                            class="p-3 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-[#0F4C81]"
                        ></textarea>
                    </div>

                    <!-- Tindak Lanjut -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700">3. Pesan Tindak Lanjut untuk Shift Berikutnya</label>
                        <textarea 
                            wire:model="tindak_lanjut" 
                            rows="2" 
                            placeholder="Instruksi pengerjaan lanjutan atau area yang perlu diawasi khusus..." 
                            class="p-3 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-[#0F4C81]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                        <button 
                            wire:click="$set('isModalOpen', false)" 
                            type="button" 
                            class="h-10 px-4 rounded-xl bg-white border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="h-10 px-5 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-semibold text-xs shadow-sm transition-all"
                        >
                            Simpan Logbook Shift
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
