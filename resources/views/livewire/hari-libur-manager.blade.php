<div>
    <!-- Top Breadcrumb & Action Hub -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Plant 1 - Karawang Barat</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span>Konfigurasi &amp; Master</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-semibold">Hari Libur</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1 flex-wrap">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Kalender Hari Libur Pabrik</h1>
                <span class="px-2.5 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-bold tracking-wider uppercase">
                    SKB 3 Menteri &amp; Perusahaan
                </span>
            </div>
            <p class="text-sm text-slate-500 max-w-4xl">
                Daftar tanggal libur resmi nasional dan cuti bersama perusahaan. Digunakan oleh generator otomatis jadwal shift untuk menandai libur staf GS dan tarif lembur shift regu.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <button 
                wire:click="openCreateModal"
                type="button" 
                class="h-10 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-semibold text-xs shadow-md hover:shadow-lg active:scale-[0.98] transition-all flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>+ Tambah Hari Libur</span>
            </button>
        </div>
    </div>

    <!-- Toolbar Filters -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-600">Filter Tahun:</span>
            <input 
                wire:model.live="filterTahun" 
                type="number" 
                min="2020" 
                max="2035" 
                class="h-10 px-3 w-28 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
            >
        </div>

        <span class="text-xs text-slate-500 font-medium">
            Total Libur Terdaftar: <b class="text-slate-900">{{ $hariLiburs->count() }} Hari</b>
        </span>
    </div>

    <!-- Holidays Table -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm text-slate-800">
            <thead class="bg-slate-50 text-slate-600 font-semibold text-xs uppercase tracking-wider border-b border-slate-200">
                <tr>
                    <th class="py-3.5 px-4 w-44">Tanggal Libur</th>
                    <th class="py-3.5 px-4">Nama / Keterangan Libur</th>
                    <th class="py-3.5 px-4 w-36">Kategori</th>
                    <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($hariLiburs as $hl)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900 text-xs">
                            {{ $hl->tanggal->format('d-m-Y') }}
                            <span class="block text-[11px] font-normal text-slate-500">
                                {{ $hl->tanggal->translatedFormat('l') }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-900 text-sm">
                            {{ $hl->keterangan }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                {{ $hl->jenis === 'NASIONAL' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}
                            ">
                                {{ $hl->jenis === 'NASIONAL' ? 'Libur Nasional' : 'Libur Perusahaan' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button 
                                    wire:click="openEditModal('{{ $hl->tanggal->format('Y-m-d') }}')"
                                    type="button" 
                                    class="p-1.5 rounded-lg text-slate-500 hover:text-[#0F4C81] hover:bg-slate-100 transition-colors cursor-pointer"
                                    title="Ubah hari libur"
                                >
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>
                                <button 
                                    wire:click="hapusHariLibur('{{ $hl->tanggal->format('Y-m-d') }}')"
                                    wire:confirm="Yakin ingin menghapus hari libur {{ $hl->keterangan }}?"
                                    type="button" 
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                    title="Hapus hari libur"
                                >
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-slate-500">
                            <span class="material-symbols-outlined text-[48px] text-slate-300">event_busy</span>
                            <p class="text-base font-semibold mt-2">Tidak ada data hari libur pada tahun ini</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL TAMBAH / UBAH HARI LIBUR -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
                <div class="h-16 px-6 border-b border-slate-200 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-rose-600 text-[24px]">event_available</span>
                        <h3 class="font-bold text-slate-900 text-base">
                            {{ $isEditMode ? 'Ubah Hari Libur' : 'Tambah Hari Libur Baru' }}
                        </h3>
                    </div>
                    <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="simpanHariLibur" class="p-6 flex flex-col gap-4 text-xs">
                    <!-- Tanggal -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700">Tanggal Libur *</label>
                        <input 
                            wire:model="tanggal" 
                            type="date" 
                            class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81] {{ $isEditMode ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-white' }}"
                            {{ $isEditMode ? 'readonly' : '' }}
                        >
                        @error('tanggal') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700">Keterangan / Nama Hari Libur *</label>
                        <input 
                            wire:model="keterangan" 
                            type="text" 
                            placeholder="Contoh: Hari Kemerdekaan RI, Cuti Bersama Idul Fitri..." 
                            class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]"
                        >
                        @error('keterangan') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jenis Libur -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700">Jenis Hari Libur *</label>
                        <select wire:model="jenis" class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]">
                            <option value="NASIONAL">Libur Nasional (SKB 3 Menteri)</option>
                            <option value="PERUSAHAAN">Libur Khusus Perusahaan / Off Pabrik</option>
                        </select>
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
                            {{ $isEditMode ? 'Perbarui Hari Libur' : 'Simpan Hari Libur' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
