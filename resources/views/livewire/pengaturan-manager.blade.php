<div>
    <!-- Top Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Konfigurasi &amp; Master</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-medium">Pengaturan Sistem</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1">
                <h1 class="text-xl font-semibold text-slate-900">Pengaturan Parameter Operasional</h1>
            </div>
            <p class="text-xs text-slate-500 max-w-3xl">
                Parameter aturan bisnis absensi, toleransi keterlambatan, jendela waktu kehadiran, dan ambang batas approval cuti.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-blue-50 text-[#0F4C81] border border-blue-200 text-xs font-medium flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">lock</span>
                <span>Khusus Admin HR</span>
            </span>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Settings Card List -->
    <div class="bg-white rounded-xl border border-slate-200 divide-y divide-slate-100 overflow-hidden shadow-sm">
        @foreach($allSettings as $item)
            <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/50 transition-colors">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-[#0F4C81] flex items-center justify-center shrink-0 mt-0.5">
                        <span class="material-symbols-outlined text-[20px]">tune</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold text-sm text-slate-900 font-mono">{{ $item->kunci }}</span>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                            {{ $item->keterangan ?? 'Parameter aturan sistem pabrik.' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4 shrink-0 justify-between md:justify-end">
                    <div class="flex flex-col items-end">
                        <span class="text-lg font-bold text-slate-900 font-mono">{{ $item->nilai }}</span>
                        <span class="text-[11px] text-slate-400">
                            @if(str_contains($item->kunci, 'menit')) Menit
                            @elseif(str_contains($item->kunci, 'jam')) Jam
                            @elseif(str_contains($item->kunci, 'hari')) Hari
                            @else Satuan
                            @endif
                        </span>
                    </div>

                    <button 
                        wire:click="editSetting('{{ $item->kunci }}')"
                        type="button" 
                        class="h-9 px-3.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-medium flex items-center gap-1.5 transition-colors cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        <span>Ubah Nilai</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Edit Setting Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-[#0F4C81]">tune</span>
                        <h3 class="font-semibold text-sm text-slate-900">Ubah Parameter Sistem</h3>
                    </div>
                    <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="simpanSetting" class="p-5 flex flex-col gap-4">
                    <div>
                        <span class="text-xs font-medium text-slate-500">Nama Kunci:</span>
                        <div class="font-mono text-xs font-semibold text-slate-800 mt-1">{{ $selectedKey }}</div>
                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $selectedDescription }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Nilai Parameter</label>
                        <input 
                            wire:model="selectedValue" 
                            type="number" 
                            class="w-full h-10 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-800 font-mono focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81]"
                            required
                        >
                        @error('selectedValue') 
                            <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 mt-2">
                        <button 
                            wire:click="$set('isModalOpen', false)" 
                            type="button" 
                            class="h-9 px-4 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50 cursor-pointer"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="h-9 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white text-xs font-medium transition-colors cursor-pointer"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
