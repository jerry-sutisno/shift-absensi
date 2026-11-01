<div>
    <!-- Top Action Hub -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-0.5">
            <h1 class="text-xl font-semibold text-slate-900">Pola Rotasi &amp; Generator Jadwal</h1>
            <p class="text-xs text-slate-500 max-w-3xl">
                Pengaturan siklus rotasi regu kerja (Grup A - D) dan pembuatan jadwal otomatis untuk kalender bulanan pabrik.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <button 
                type="button" 
                class="h-9 px-3.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">bookmark</span>
                <span>Simpan Preset</span>
            </button>
            <button 
                type="button" 
                class="h-9 px-3.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                <span>Impor Template</span>
            </button>
            <button 
                wire:click="generateJadwal" 
                type="button" 
                class="h-9 px-3.5 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">event_available</span>
                <span>Terapkan ke Kalender</span>
            </button>
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN: Pattern Matrix (8 Columns) -->
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-6">
            
            <!-- Matrix Card -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 md:p-6">
                <!-- Header & Preset Selector -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900 leading-tight">
                            Konfigurasi Siklus Pola Regu ({{ $panjangSiklus }} Hari)
                        </h2>
                        <span class="text-xs text-slate-500 mt-0.5">
                            Pola berulang sepanjang bulan terpilih. Klik sel shift untuk mengubah penugasan.
                        </span>
                    </div>

                    <!-- Preset Selector Dropdown -->
                    <div class="flex flex-col gap-1 min-w-[220px]">
                        <span class="text-xs font-medium text-slate-500">Preset Pola</span>
                        <select 
                            wire:model.live="selectedPolaId"
                            class="h-9 px-3 rounded-xl bg-white border border-slate-200 text-xs font-normal text-slate-700 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81]"
                        >
                            @foreach ($polas as $p)
                                <option value="{{ $p->id_pola }}">{{ $p->nama_pola }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Matrix Table -->
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="py-2.5 px-3 text-xs font-medium text-slate-600 w-44">
                                    Regu Kerja
                                </th>
                                @for ($h = 1; $h <= $panjangSiklus; $h++)
                                    <th class="py-2.5 px-2 text-xs font-medium text-center text-slate-700 min-w-[84px]">
                                        Hari {{ $h }}
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($grups as $grup)
                                <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                    <!-- Regu Header Cell -->
                                    <td class="py-3 px-3">
                                        <div class="flex flex-col">
                                            <span class="font-medium text-slate-900 text-xs leading-tight">
                                                {{ $grup->nama_grup }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 mt-0.5">
                                                {{ $grup->karyawan_count }} Operator
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Shift Cells Hari 1..8 -->
                                    @for ($h = 1; $h <= $panjangSiklus; $h++)
                                        @php
                                            $shiftCode = $matrix[$grup->id_grup][$h] ?? 'OFF';
                                        @endphp
                                        <td class="py-2 px-1 text-center">
                                            <button 
                                                wire:click="cycleShiftCell({{ $grup->id_grup }}, {{ $h }})"
                                                type="button"
                                                title="Klik untuk rotasi shift"
                                                class="w-full py-1 px-1.5 rounded-lg text-xs font-medium transition-colors duration-150 flex items-center justify-between gap-1 cursor-pointer
                                                    @if($shiftCode === 'S1') bg-amber-50 text-amber-900 border border-amber-200 hover:bg-amber-100
                                                    @elseif($shiftCode === 'S2') bg-orange-50 text-orange-950 border border-orange-200 hover:bg-orange-100
                                                    @elseif($shiftCode === 'S3') bg-indigo-50 text-indigo-950 border border-indigo-200 hover:bg-indigo-100
                                                    @elseif($shiftCode === 'GS') bg-blue-50 text-blue-900 border border-blue-200 hover:bg-blue-100
                                                    @else bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100 @endif
                                                "
                                            >
                                                <span class="truncate">
                                                    @if($shiftCode === 'S1') S1 &bull; Pagi
                                                    @elseif($shiftCode === 'S2') S2 &bull; Siang
                                                    @elseif($shiftCode === 'S3') S3 &bull; Malam
                                                    @elseif($shiftCode === 'GS') GS &bull; Normal
                                                    @else OFF &bull; Libur @endif
                                                </span>
                                                <span class="material-symbols-outlined text-[14px] text-slate-400">unfold_more</span>
                                            </button>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach

                            <!-- Summary Row: Cakupan per Hari -->
                            <tr class="bg-slate-50 border-t border-slate-200">
                                <td class="py-2.5 px-3">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-slate-700">Cakupan Regu</span>
                                        <span class="text-[11px] text-slate-400">Kebutuhan Shift</span>
                                    </div>
                                </td>
                                @for ($h = 1; $h <= $panjangSiklus; $h++)
                                    @php
                                        $cov = $coverage[$h] ?? null;
                                    @endphp
                                    <td class="py-2 px-1 text-center font-mono">
                                        <div class="p-1 rounded-lg bg-white border border-slate-200 text-[10px] flex flex-col gap-0.5">
                                            <div class="flex items-center justify-between text-slate-600">
                                                <span>S1:</span>
                                                <span class="font-medium text-slate-800">{{ $cov['s1_op'] ?? 0 }}</span>
                                            </div>
                                            <div class="flex items-center justify-between text-slate-600">
                                                <span>S2:</span>
                                                <span class="font-medium text-slate-800">{{ $cov['s2_op'] ?? 0 }}</span>
                                            </div>
                                            <div class="flex items-center justify-between text-slate-600">
                                                <span>S3:</span>
                                                <span class="font-medium text-slate-800">{{ $cov['s3_op'] ?? 0 }}</span>
                                            </div>
                                            <div class="pt-0.5 border-t border-slate-100 font-medium {{ ($cov['is_complete'] ?? false) ? 'text-emerald-600' : 'text-rose-600' }}">
                                                {{ ($cov['is_complete'] ?? false) ? 'Lengkap' : 'Kurang' }}
                                            </div>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Shift Chips Legend -->
                <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                    <span class="text-xs font-medium text-slate-500">Keterangan:</span>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                            <span>S1 Pagi (07:00 - 15:00)</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                            <span>S2 Siang (15:00 - 23:00)</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                            <span>S3 Malam (23:00 - 07:00)</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <span>GS Normal (08:00 - 17:00)</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                            <span>OFF Libur</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Ketentuan Rotasi -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 md:p-5 flex flex-col gap-1.5">
                <span class="text-xs font-medium text-slate-500">Ketentuan Jam Kerja</span>
                <h3 class="text-sm font-semibold text-slate-900">Istirahat Antar Shift</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Siklus rotasi dirancang untuk menjamin jeda istirahat minimal 24 jam saat rotasi dari Shift 3 (Malam) menuju hari OFF, guna menjaga kebugaran kerja operator pabrik.
                </p>
            </div>
        </div>

        <!-- RIGHT COLUMN: Generator Parameters & Manpower Validation -->
        <div class="lg:col-span-5 xl:col-span-4 flex flex-col gap-6">
            
            <!-- Parameter Generator Jadwal Card -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 md:p-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h2 class="text-sm font-semibold text-slate-900">Parameter Generator</h2>
                    <span class="w-2 h-2 rounded-full bg-emerald-500" title="Siap diproses"></span>
                </div>

                <form wire:submit.prevent="generateJadwal" class="flex flex-col gap-4">
                    <!-- Departemen -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-medium text-slate-700">Departemen <span class="text-rose-500">*</span></label>
                        <select 
                            wire:model.live="targetDepartemenId"
                            class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150"
                        >
                            @foreach ($departemens as $dept)
                                <option value="{{ $dept->id_departemen }}">
                                    {{ $dept->nama_departemen }} ({{ $dept->karyawan_count }} Karyawan)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Target Periode Bulan & Tahun -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-medium text-slate-700">Periode Bulan &amp; Tahun <span class="text-rose-500">*</span></label>
                        <input 
                            wire:model.live="targetBulan"
                            type="month" 
                            class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150"
                        >
                    </div>

                    <!-- Pola Rotasi Terpilih -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-medium text-slate-700">Pola Rotasi Terpilih</label>
                        <div class="h-10 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-700 flex items-center justify-between">
                            <span>{{ $pola ? $pola->nama_pola : 'Rotasi 4 Regu 3 Shift' }}</span>
                            <span class="material-symbols-outlined text-[16px] text-slate-400">lock</span>
                        </div>
                    </div>

                    <!-- Titik Awal Siklus -->
                    <div class="flex flex-col gap-1">
                        <label class="text-xs font-medium text-slate-700">Tanggal Mulai Siklus (Hari ke-1) <span class="text-rose-500">*</span></label>
                        <input 
                            wire:model="tanggalMulaiSiklus"
                            type="date" 
                            class="w-full h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150"
                        >
                        <span class="text-[11px] text-slate-400">
                            Hari ke-1 siklus dimulai pada tanggal ini.
                        </span>
                    </div>

                    <!-- Checkboxes -->
                    <div class="flex flex-col gap-2.5 pt-1">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input 
                                wire:model="sesuaikanHariLibur"
                                type="checkbox" 
                                class="mt-0.5 w-4 h-4 rounded text-[#0F4C81] focus:ring-[#0F4C81] border-slate-300"
                            >
                            <span class="text-xs text-slate-700">Sesuaikan dengan hari libur nasional</span>
                        </label>

                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input 
                                wire:model="kunciNonRotasi"
                                type="checkbox" 
                                class="mt-0.5 w-4 h-4 rounded text-[#0F4C81] focus:ring-[#0F4C81] border-slate-300"
                            >
                            <span class="text-xs text-slate-700">Pertahankan jadwal General Shift (GS) untuk staf non-regu</span>
                        </label>
                    </div>

                    <!-- Status Summary -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-500 text-[18px]">info</span>
                        <span>{{ $selectedDept->karyawan_count ?? 0 }} karyawan departemen {{ $selectedDept->nama_departemen ?? 'terpilih' }} siap diproses.</span>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full h-10 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-medium text-xs transition-colors duration-150 flex items-center justify-center gap-2 cursor-pointer mt-1"
                    >
                        <span class="material-symbols-outlined text-[18px]">calendar_add_on</span>
                        <span>Buat Jadwal Bulanan</span>
                    </button>
                </form>
            </div>

            <!-- Validasi Manpower Card -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 md:p-6 flex flex-col gap-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-900">Ringkasan Validasi</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Terpenuhi
                    </span>
                </div>

                <!-- 3-Column Metrics Stats -->
                <div class="grid grid-cols-3 gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                    <div class="flex flex-col">
                        <span class="text-[11px] text-slate-500">Hari Terpenuhi</span>
                        <span class="text-base font-semibold text-slate-900 mt-1">31 / 31</span>
                        <span class="text-[10px] text-emerald-600 font-medium">100%</span>
                    </div>
                    <div class="flex flex-col border-x border-slate-200 px-1">
                        <span class="text-[11px] text-slate-500">Defisit Slot</span>
                        <span class="text-base font-semibold text-slate-900 mt-1">0</span>
                        <span class="text-[10px] text-slate-500">Lengkap</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[11px] text-slate-500">Rata-rata Jam</span>
                        <span class="text-base font-semibold text-slate-900 mt-1">40.0</span>
                        <span class="text-[10px] text-slate-500">Jam/Minggu</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

