<div>
    <!-- Top Breadcrumb & Algoritma Badge -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Plant 1 - Karawang Barat</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span>Perencanaan Jadwal & Roster</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-semibold">Pola Rotasi & Generator Jadwal</span>
            </nav>
            <div class="flex items-center gap-3 mt-1 flex-wrap">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pola Rotasi & Generator Otomatis Jadwal Shift</h1>
                <span class="px-2.5 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 text-[11px] font-bold tracking-wider uppercase">
                    Mode Konfigurasi Algoritma Roster
                </span>
            </div>
            <p class="text-sm text-slate-500 max-w-4xl">
                Atur siklus rotasi regu kerja (Grup A-D) dan terapkan otomatis ke kalender bulanan pabrik dengan validasi kecukupan manpower presisi sesuai regulasi jam kerja Depnaker.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <div class="hidden xl:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                <span>Algoritma Penjadwalan Aktif &bull; V4.2-Industrial</span>
            </div>
            <button 
                type="button" 
                class="h-10 px-4 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">bookmark</span>
                <span>Simpan Pola Preset</span>
            </button>
            <button 
                type="button" 
                class="h-10 px-4 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition-colors flex items-center gap-1.5 shadow-xs cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                <span>Import Template Pabrik</span>
            </button>
            <button 
                wire:click="generateJadwal" 
                type="button" 
                class="h-10 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-semibold text-xs shadow-md hover:shadow-lg active:scale-[0.98] transition-all flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">event_available</span>
                <span>+ Terapkan ke Kalender</span>
            </button>
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN: Pattern Matrix (8 Columns) -->
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-6">
            
            <!-- Matrix Card -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5 md:p-6">
                <!-- Header & Preset Selector -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0F4C81] flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-[22px]">sync_alt</span>
                        </div>
                        <div class="flex flex-col">
                            <h2 class="text-base font-bold text-slate-900 leading-tight">
                                Konfigurasi Siklus Pola Regu ({{ $panjangSiklus }}-Hari Siklus)
                            </h2>
                            <span class="text-xs text-slate-500 mt-0.5">
                                Pola akan berulang terus sepanjang bulan terpilih. Klik sel shift untuk mengubah penugasan.
                            </span>
                        </div>
                    </div>

                    <!-- Preset Selector Dropdown -->
                    <div class="flex flex-col gap-1 min-w-[240px]">
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Preset Pola Terverifikasi</span>
                        <select 
                            wire:model.live="selectedPolaId"
                            class="h-10 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
                        >
                            @foreach ($polas as $p)
                                <option value="{{ $p->id_pola }}">{{ $p->nama_pola }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Matrix Table -->
                <div class="overflow-x-auto mt-5">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/70">
                                <th class="py-3 px-3 text-xs font-bold uppercase tracking-wider text-slate-600 w-44">
                                    Regu & Operator
                                </th>
                                @for ($h = 1; $h <= $panjangSiklus; $h++)
                                    <th class="py-3 px-2 text-xs font-bold text-center text-slate-700 min-w-[90px]">
                                        Hari {{ $h }}
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($grups as $grup)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <!-- Regu Header Cell -->
                                    <td class="py-3 px-3">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-900 text-sm leading-tight">
                                                {{ $grup->nama_grup }}
                                            </span>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="text-xs font-medium text-slate-500">
                                                    {{ $grup->karyawan_count }} Operator
                                                </span>
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    Terisi Penuh
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Shift Cells Hari 1..8 -->
                                    @for ($h = 1; $h <= $panjangSiklus; $h++)
                                        @php
                                            $shiftCode = $matrix[$grup->id_grup][$h] ?? 'OFF';
                                        @endphp
                                        <td class="py-2.5 px-1.5 text-center">
                                            <div class="relative group inline-block w-full">
                                                <button 
                                                    wire:click="cycleShiftCell({{ $grup->id_grup }}, {{ $h }})"
                                                    type="button"
                                                    title="Klik untuk rotasi shift (S1 -> S2 -> S3 -> OFF)"
                                                    class="w-full py-1.5 px-2 rounded-lg text-xs font-bold transition-all transform active:scale-95 shadow-2xs flex items-center justify-between gap-1 cursor-pointer
                                                        @if($shiftCode === 'S1') bg-amber-100 text-amber-900 border border-amber-300 hover:bg-amber-200
                                                        @elseif($shiftCode === 'S2') bg-orange-100 text-orange-950 border border-orange-300 hover:bg-orange-200
                                                        @elseif($shiftCode === 'S3') bg-indigo-100 text-indigo-950 border border-indigo-300 hover:bg-indigo-200
                                                        @elseif($shiftCode === 'GS') bg-blue-100 text-blue-900 border border-blue-300 hover:bg-blue-200
                                                        @else bg-slate-100 text-slate-700 border border-slate-300 hover:bg-slate-200 @endif
                                                    "
                                                >
                                                    <span class="truncate">
                                                        @if($shiftCode === 'S1') S1 &bull; Pagi
                                                        @elseif($shiftCode === 'S2') S2 &bull; Siang
                                                        @elseif($shiftCode === 'S3') S3 &bull; Malam
                                                        @elseif($shiftCode === 'GS') GS &bull; Normal
                                                        @else OFF &bull; Libur @endif
                                                    </span>
                                                    <span class="material-symbols-outlined text-[14px] opacity-60">expand_more</span>
                                                </button>
                                            </div>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach

                            <!-- Summary Row: Cakupan per Hari -->
                            <tr class="bg-blue-50/40 border-t-2 border-blue-100">
                                <td class="py-3 px-3">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-800 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[16px] text-blue-600">verified</span>
                                            <span>Cakupan per Hari</span>
                                        </span>
                                        <span class="text-[10px] text-blue-700 font-semibold mt-0.5">
                                            Semua Shift Terpenuhi
                                        </span>
                                    </div>
                                </td>
                                @for ($h = 1; $h <= $panjangSiklus; $h++)
                                    @php
                                        $cov = $coverage[$h] ?? null;
                                    @endphp
                                    <td class="py-2.5 px-1.5 text-center font-mono">
                                        <div class="p-1.5 rounded-lg bg-white border border-slate-200 shadow-2xs text-[11px] flex flex-col gap-0.5">
                                            <div class="flex items-center justify-between text-amber-700 font-bold">
                                                <span>S1:</span>
                                                <span>{{ $cov['s1_op'] ?? 0 }}</span>
                                            </div>
                                            <div class="flex items-center justify-between text-orange-700 font-bold">
                                                <span>S2:</span>
                                                <span>{{ $cov['s2_op'] ?? 0 }}</span>
                                            </div>
                                            <div class="flex items-center justify-between text-indigo-700 font-bold">
                                                <span>S3:</span>
                                                <span>{{ $cov['s3_op'] ?? 0 }}</span>
                                            </div>
                                            <div class="mt-0.5 pt-0.5 border-t border-slate-100 text-[10px] font-extrabold {{ ($cov['is_complete'] ?? false) ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ ($cov['is_complete'] ?? false) ? '✓ 100%' : '! Defisit' }}
                                            </div>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Shift Chips Legend -->
                <div class="mt-6 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <span class="font-bold text-slate-500 uppercase tracking-wider text-[11px]">Keterangan Shift :</span>
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded bg-amber-400"></span>
                            <span class="font-semibold text-slate-700">S1 Pagi (07:00 - 15:00)</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded bg-orange-500"></span>
                            <span class="font-semibold text-slate-700">S2 Siang (15:00 - 23:00)</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded bg-indigo-600"></span>
                            <span class="font-semibold text-slate-700">S3 Malam (23:00 - 07:00)</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded bg-blue-600"></span>
                            <span class="font-semibold text-slate-700">GS Normal (08:00 - 17:00)</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded bg-slate-400"></span>
                            <span class="font-semibold text-slate-700">OFF Libur Regu</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- SOP & Health Safety Card (Matches bottom info on Layar 5) -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-4 md:p-5 flex flex-col sm:flex-row items-center gap-4">
                <div class="w-20 h-20 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center text-slate-400">
                    <span class="material-symbols-outlined text-[36px] text-[#0F4C81]">factory</span>
                </div>
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Line Produksi 1-5</span>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-xs font-medium text-slate-500">Standar SOP Rotasi ISO 45001</span>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">Protokol Penukaran Shift Aman</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Siklus Continental menjamin jeda istirahat minimal 24 jam saat rotasi dari Shift 3 (Malam) menuju hari OFF, mematuhi standar ergonomi kerja fisik operator pabrik dan UU Cipta Kerja No. 11/2020.
                    </p>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Generator Parameters & Manpower Validation (4-5 Columns) -->
        <div class="lg:col-span-5 xl:col-span-4 flex flex-col gap-6">
            
            <!-- Parameter Generator Jadwal Card -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5 md:p-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0F4C81] text-[22px]">tune</span>
                        <h2 class="text-base font-bold text-slate-900">Parameter Generator Jadwal</h2>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" title="Generator siap dijalankan"></span>
                </div>

                <form wire:submit.prevent="generateJadwal" class="flex flex-col gap-4">
                    <!-- Departemen / Sektor Pabrik -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-700">Departemen / Sektor Pabrik *</label>
                        <select 
                            wire:model.live="targetDepartemenId"
                            class="w-full h-11 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
                        >
                            @foreach ($departemens as $dept)
                                <option value="{{ $dept->id_departemen }}">
                                    {{ $dept->nama_departemen }} ({{ $dept->karyawan_count }} Karyawan)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Target Periode Bulan & Tahun -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-700">Target Periode Bulan & Tahun *</label>
                        <div class="relative">
                            <input 
                                wire:model.live="targetBulan"
                                type="month" 
                                class="w-full h-11 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
                            >
                        </div>
                    </div>

                    <!-- Pola Rotasi Terpilih (Read-only Lock) -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-700">Pola Rotasi Terpilih</label>
                        <div class="h-11 px-3.5 rounded-xl bg-slate-100 border border-slate-200 text-sm font-semibold text-slate-700 flex items-center justify-between">
                            <span>{{ $pola ? $pola->nama_pola : 'Rotasi 4 Regu 3 Shift' }}</span>
                            <span class="material-symbols-outlined text-[18px] text-slate-400">lock</span>
                        </div>
                    </div>

                    <!-- Titik Awal Siklus -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-slate-700">Titik Awal Siklus (Hari ke-1) *</label>
                        <input 
                            wire:model="tanggalMulaiSiklus"
                            type="date" 
                            class="w-full h-11 px-3.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-semibold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0F4C81]"
                        >
                        <span class="text-[11px] text-slate-500">
                            Pola dimulai dari Hari 1 (Regu A: S1 Pagi) pada tanggal ini.
                        </span>
                    </div>

                    <!-- Checkboxes -->
                    <div class="flex flex-col gap-3 pt-2">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input 
                                wire:model="sesuaikanHariLibur"
                                type="checkbox" 
                                class="mt-1 w-4 h-4 rounded text-[#0F4C81] focus:ring-[#0F4C81] border-slate-300"
                            >
                            <div class="flex flex-col text-xs">
                                <span class="font-semibold text-slate-800">Otomatis sesuaikan Hari Libur Nasional (SKB 3 Menteri)</span>
                                <span class="text-slate-500">Kompensasi tarif lembur reguler otomatis diaktifkan untuk operator yang masuk.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input 
                                wire:model="kunciNonRotasi"
                                type="checkbox" 
                                class="mt-1 w-4 h-4 rounded text-[#0F4C81] focus:ring-[#0F4C81] border-slate-300"
                            >
                            <div class="flex flex-col text-xs">
                                <span class="font-semibold text-slate-800">Kunci staf non-rotasi (General Shift)</span>
                                <span class="text-slate-500">Tetap pertahankan jam kerja Senin-Jumat GS untuk staf tanpa grup.</span>
                            </div>
                        </label>
                    </div>

                    <!-- Status Notification Toast Banner -->
                    <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-2.5">
                        <span class="material-symbols-outlined text-emerald-600 text-[20px] shrink-0 mt-0.5">check_circle</span>
                        <div class="flex flex-col text-xs">
                            <span class="font-bold">Simulasi Siklus Siap</span>
                            <span class="text-emerald-700">
                                {{ $selectedDept->karyawan_count ?? 0 }} karyawan departemen {{ $selectedDept->nama_departemen ?? 'terpilih' }} siap di-generate ke kalender draf.
                            </span>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Validasi Manpower & Kuota Card -->
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5 md:p-6 flex flex-col gap-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0F4C81] text-[20px]">assignment_turned_in</span>
                        <h3 class="text-sm font-bold text-slate-900">Validasi Manpower & Kuota</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                        Target Kuota Optimal
                    </span>
                </div>

                <!-- Progress Bar -->
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-700">Progres Validasi Kuota (Bulan Ini)</span>
                        <span class="font-bold text-emerald-600">100% Terpenuhi</span>
                    </div>
                    <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" style="width: 100%"></div>
                    </div>
                </div>

                <!-- Breakdown Status Items -->
                <div class="flex flex-col gap-2">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800">Hari Kerja Reguler</span>
                                <span class="text-slate-500">S1, S2, S3 selalu terisi 1 regu penuh</span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">
                            Optimal
                        </span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-blue-600 text-[18px]">hourglass_empty</span>
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-800">Istirahat Antar Shift</span>
                                <span class="text-slate-500">Jeda istirahat &gt; 24 jam saat rotasi S3 ke OFF</span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold text-[10px] border border-blue-200">
                            Sesuai SOP
                        </span>
                    </div>
                </div>

                <!-- 3-Column Metrics Stats -->
                <div class="grid grid-cols-3 gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Hari Terpenuhi</span>
                        <span class="text-base font-extrabold text-slate-900 mt-0.5">31 / 31</span>
                        <span class="text-[10px] font-semibold text-emerald-600">100% Kuota OK</span>
                    </div>
                    <div class="flex flex-col border-x border-slate-200 px-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Slot Defisit</span>
                        <span class="text-base font-extrabold text-slate-900 mt-0.5">0 Slot</span>
                        <span class="text-[10px] font-semibold text-slate-500">Cakupan Penuh</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Rata Jam/Minggu</span>
                        <span class="text-base font-extrabold text-slate-900 mt-0.5">40.0 Jam</span>
                        <span class="text-[10px] font-semibold text-blue-600">UU Ciptaker</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-2 pt-1">
                    <button 
                        wire:click="generateJadwal" 
                        type="button" 
                        class="w-full h-12 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] active:scale-[0.98] text-white font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[20px]">calendar_add_on</span>
                        <span>Terapkan ke Draf Kalender Bulanan</span>
                    </button>
                    <button 
                        type="button" 
                        class="w-full h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <span class="material-symbols-outlined text-[18px]">auto_fix_high</span>
                        <span>Perbaiki Otomatis Slot Defisit (Auto-Fill)</span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
