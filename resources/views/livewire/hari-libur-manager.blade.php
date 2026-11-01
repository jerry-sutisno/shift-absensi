<div>
    <!-- Top Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Konfigurasi &amp; master</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-medium">Hari libur</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1">
                <h1 class="text-xl font-semibold text-slate-900">Kalender hari libur pabrik</h1>
            </div>
            <p class="text-xs text-slate-500 max-w-3xl">
                Daftar tanggal libur resmi nasional dan cuti bersama perusahaan untuk penandaan jadwal shift dan perhitungan lembur.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0">
            <button 
                wire:click="openCreateModal"
                type="button" 
                class="h-9 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#0c3c66] text-white font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Tambah hari libur</span>
            </button>
        </div>
    </div>

    <!-- Toolbar Filters -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium text-slate-600">Filter tahun:</span>
            <input 
                wire:model.live="filterTahun" 
                type="number" 
                min="2020" 
                max="2035" 
                class="h-9 px-3 w-28 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
            >
        </div>

        <span class="text-xs text-slate-500 font-normal">
            Total libur terdaftar: <b class="text-slate-800 font-medium">{{ $hariLiburs->count() }} hari</b>
        </span>
    </div>

    <!-- Holidays Table -->
    <div class="rounded-xl border border-slate-200 bg-white overflow-hidden">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-50 text-slate-600 font-medium text-xs border-b border-slate-200">
                <tr>
                    <th class="py-3 px-4 w-44">Tanggal libur</th>
                    <th class="py-3 px-4">Keterangan</th>
                    <th class="py-3 px-4 w-40">Kategori</th>
                    <th class="py-3 px-4 text-center w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($hariLiburs as $hl)
                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="py-3 px-4 font-mono text-slate-800">
                            {{ $hl->tanggal->format('d-m-Y') }}
                            <span class="block text-[11px] text-slate-500">
                                {{ $hl->tanggal->translatedFormat('l') }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-medium text-slate-900">
                            {{ $hl->keterangan }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium
                                {{ $hl->jenis === 'NASIONAL' ? 'bg-rose-50 text-rose-800 border border-rose-200' : 'bg-purple-50 text-purple-800 border border-purple-200' }}
                            ">
                                {{ $hl->jenis === 'NASIONAL' ? 'Libur nasional' : 'Libur perusahaan' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button 
                                    wire:click="openEditModal('{{ $hl->tanggal->format('Y-m-d') }}')"
                                    type="button" 
                                    class="p-1 rounded-xl text-slate-500 hover:text-[#0F4C81] hover:bg-slate-100 transition-colors duration-150 cursor-pointer"
                                    title="Ubah hari libur"
                                >
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </button>
                                <button 
                                    wire:click="hapusHariLibur('{{ $hl->tanggal->format('Y-m-d') }}')"
                                    wire:confirm="Yakin ingin menghapus hari libur {{ $hl->keterangan }}?"
                                    type="button" 
                                    class="p-1 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors duration-150 cursor-pointer"
                                    title="Hapus hari libur"
                                >
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-slate-500">
                            Tidak ada data hari libur pada tahun yang dipilih
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL TAMBAH / UBAH HARI LIBUR -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
            <div class="w-full max-w-md bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden flex flex-col">
                <div class="h-14 px-5 border-b border-slate-200 flex items-center justify-between bg-white">
                    <h3 class="font-semibold text-slate-900 text-sm">
                        {{ $isEditMode ? 'Ubah hari libur' : 'Tambah hari libur baru' }}
                    </h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="simpanHariLibur" class="p-5 flex flex-col gap-4 text-xs">
                    <!-- Tanggal -->
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">Tanggal libur *</label>
                        <input 
                            wire:model="tanggal" 
                            type="date" 
                            class="h-9 px-3 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81] {{ $isEditMode ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : 'bg-slate-50 focus:bg-white' }}"
                            {{ $isEditMode ? 'readonly' : '' }}
                        >
                        @error('tanggal') <span class="text-rose-600 font-medium text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">Nama atau keterangan hari libur *</label>
                        <input 
                            wire:model="keterangan" 
                            type="text" 
                            placeholder="Contoh: Hari Kemerdekaan RI, Cuti Bersama Idul Fitri..." 
                            class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]"
                        >
                        @error('keterangan') <span class="text-rose-600 font-medium text-[11px]">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jenis Libur -->
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">Jenis hari libur *</label>
                        <select wire:model="jenis" class="h-9 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0F4C81] focus:border-[#0F4C81]">
                            <option value="NASIONAL">Libur nasional</option>
                            <option value="PERUSAHAAN">Libur khusus perusahaan</option>
                        </select>
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
                            {{ $isEditMode ? 'Perbarui hari libur' : 'Simpan hari libur' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
