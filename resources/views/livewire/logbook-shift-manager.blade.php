<div>
    <!-- Top Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Operasional</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-medium">Logbook shift</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1">
                <h1 class="text-xl font-semibold text-slate-900">Logbook serah terima shift</h1>
            </div>
            <p class="text-xs text-slate-500 max-w-3xl">
                Catatan ringkasan output produksi, kendala mesin atau material, dan instruksi tindak lanjut untuk supervisor dan regu shift berikutnya.
            </p>
        </div>

        @if(Auth::user()->isSupervisor())
        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <button 
                wire:click="openCreateModal"
                type="button" 
                class="h-9 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#0c3c66] text-white font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Buat catatan logbook</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Toolbar Filters -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3 w-full md:w-auto flex-wrap">
            <div class="flex items-center gap-1.5">
                <span class="text-xs font-medium text-slate-600">Tanggal:</span>
                <input 
                    wire:model.live="filterTanggal" 
                    type="date" 
                    class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
                >
            </div>

            <select 
                wire:model.live="filterDepartemen" 
                class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
            >
                <option value="">Semua departemen</option>
                @foreach ($departemens as $d)
                    <option value="{{ $d->id_departemen }}">{{ $d->nama_departemen }}</option>
                @endforeach
            </select>

            <select 
                wire:model.live="filterShift" 
                class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
            >
                <option value="">Semua shift</option>
                @foreach ($shifts as $s)
                    <option value="{{ $s->kode_shift }}">{{ $s->kode_shift }} ({{ $s->nama_shift }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Logbook Cards List -->
    <div class="flex flex-col gap-4">
        @forelse ($logbooks as $log)
            <div class="rounded-xl border border-slate-200 bg-white p-5 flex flex-col gap-4">
                <!-- Header Card -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded text-[11px] font-medium
                            @if($log->kode_shift === 'S1') bg-amber-50 text-amber-800 border border-amber-200
                            @elseif($log->kode_shift === 'S2') bg-orange-50 text-orange-800 border border-orange-200
                            @elseif($log->kode_shift === 'S3') bg-indigo-50 text-indigo-800 border border-indigo-200
                            @else bg-sky-50 text-sky-800 border border-sky-200 @endif
                        ">
                            Shift {{ $log->kode_shift }} &bull; {{ $log->shift->nama_shift }}
                        </span>
                        <span class="font-medium text-slate-900 text-sm">
                            {{ $log->departemen->nama_departemen }}
                            @if ($log->grup) &bull; <span class="text-slate-600 font-normal">{{ $log->grup->nama_grup }}</span> @endif
                        </span>
                        <span class="text-xs text-slate-500">
                            ({{ $log->tanggal->format('d M Y') }})
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <span>Dibuat oleh: <b class="text-slate-700 font-medium">{{ $log->pembuat->nama }}</b></span>
                        <span class="text-slate-300">&bull;</span>
                        <span>{{ $log->created_at->format('H:i') }} WIB</span>
                    </div>
                </div>

                <!-- 3 Structured Log Sections -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                    <!-- Ringkasan Pekerjaan -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-1">
                        <span class="font-medium text-slate-900">Ringkasan pekerjaan</span>
                        <p class="text-slate-600 leading-relaxed whitespace-pre-line text-[11px]">
                            {{ $log->ringkasan }}
                        </p>
                    </div>

                    <!-- Kendala & Masalah -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-1">
                        <span class="font-medium text-slate-900">Kendala &amp; masalah</span>
                        <p class="text-slate-600 leading-relaxed whitespace-pre-line text-[11px]">
                            {{ $log->kendala ?: 'Tidak ada kendala operasional yang dilaporkan.' }}
                        </p>
                    </div>

                    <!-- Tindak Lanjut -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-1">
                        <span class="font-medium text-slate-900">Tindak lanjut</span>
                        <p class="text-slate-600 leading-relaxed whitespace-pre-line text-[11px]">
                            {{ $log->tindak_lanjut ?: 'Lanjutkan pekerjaan sesuai target SOP harian.' }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-10 rounded-xl bg-white border border-slate-200 text-center text-slate-500 text-xs">
                Belum ada catatan logbook untuk tanggal ini.
            </div>
        @endforelse

        <div class="mt-4">
            {{ $logbooks->links() }}
        </div>
    </div>

    <!-- MODAL BUAT LOGBOOK -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
            <div class="w-full max-w-xl bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden flex flex-col">
                <div class="h-14 px-5 border-b border-slate-200 flex items-center justify-between bg-white">
                    <h3 class="font-semibold text-slate-900 text-sm">Buat logbook serah terima shift</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="simpanLogbook" class="p-5 flex flex-col gap-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="font-medium text-slate-700">Tanggal shift *</label>
                            <input 
                                wire:model="tanggal" 
                                type="date" 
                                class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
                            >
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="font-medium text-slate-700">Shift *</label>
                            <select wire:model="kode_shift" class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]">
                                @foreach ($shifts as $s)
                                    <option value="{{ $s->kode_shift }}">{{ $s->kode_shift }} ({{ $s->nama_shift }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="font-medium text-slate-700">Departemen *</label>
                            <select wire:model="id_departemen" class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]">
                                @foreach ($departemens as $d)
                                    <option value="{{ $d->id_departemen }}">{{ $d->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="font-medium text-slate-700">Regu / grup kerja (opsional)</label>
                            <select wire:model="id_grup" class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]">
                                <option value="">Tanpa regu khusus</option>
                                @foreach ($grups as $g)
                                    <option value="{{ $g->id_grup }}">{{ $g->nama_grup }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Ringkasan -->
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">1. Ringkasan pekerjaan selesai *</label>
                        <textarea 
                            wire:model="ringkasan" 
                            rows="3" 
                            placeholder="Tuliskan output lini, target kuota yang tercapai, dan aktivitas penting..." 
                            class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
                        ></textarea>
                        @error('ringkasan') <span class="text-rose-600 font-medium text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kendala -->
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">2. Kendala mesin atau bahan (jika ada)</label>
                        <textarea 
                            wire:model="kendala" 
                            rows="2" 
                            placeholder="Tuliskan downtime mesin, bahan defect, atau kendala manpower..." 
                            class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
                        ></textarea>
                    </div>

                    <!-- Tindak Lanjut -->
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">3. Pesan tindak lanjut untuk shift berikutnya</label>
                        <textarea 
                            wire:model="tindak_lanjut" 
                            rows="2" 
                            placeholder="Instruksi pengerjaan lanjutan atau area yang perlu diawasi khusus..." 
                            class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                        <button 
                            wire:click="$set('isModalOpen', false)" 
                            type="button" 
                            class="h-9 px-4 rounded-xl bg-white border border-slate-200 text-slate-700 font-medium text-xs hover:bg-slate-50 transition-colors duration-150"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="h-9 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#0c3c66] text-white font-medium text-xs transition-colors duration-150"
                        >
                            Simpan logbook
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
