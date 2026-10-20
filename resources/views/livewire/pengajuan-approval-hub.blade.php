<div>
    <!-- Top Breadcrumb & Action Hub -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-1">
            <nav class="flex items-center gap-1.5 text-xs text-slate-500 whitespace-nowrap overflow-x-auto pb-1">
                <span>Plant 1 - Karawang Barat</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span>Operasional</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-[#0F4C81] font-semibold">Pengajuan & Approval</span>
            </nav>
            <div class="flex items-center gap-2.5 mt-1 flex-wrap">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pengajuan & Approval Operasional</h1>
                <span class="px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-[11px] font-bold tracking-wider uppercase">
                    Q2 &bull; Operasi Pabrik
                </span>
            </div>
            <p class="text-sm text-slate-500 max-w-4xl">
                Kelola dan verifikasi berkas permohonan izin, cuti sakit, pertukaran giliran shift, dan SPKL lembur operator pabrik.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <button 
                wire:click="openTukarShiftModal"
                type="button" 
                class="h-10 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs shadow-xs hover:shadow active:scale-[0.98] transition-all flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">sync_alt</span>
                <span>Ajukan Tukar Shift</span>
            </button>
            <button 
                wire:click="openLemburModal"
                type="button" 
                class="h-10 px-4 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs shadow-xs hover:shadow active:scale-[0.98] transition-all flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">more_time</span>
                <span>+ Ajukan Lembur</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Metrics Cards (Matches Stitch Layar 7) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <!-- Menunggu Verifikasi -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Menunggu Verifikasi</span>
                <span class="text-3xl font-extrabold text-slate-900 mt-1">{{ $countMenunggu }}</span>
                <span class="text-xs text-amber-600 font-bold mt-0.5 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <span>Butuh Aksi Segera</span>
                </span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">hourglass_top</span>
            </div>
        </div>

        <!-- Disetujui Hari Ini -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Disetujui Hari Ini</span>
                <span class="text-3xl font-extrabold text-slate-900 mt-1">{{ $countDisetujui }}</span>
                <span class="text-xs text-emerald-600 font-semibold mt-0.5">Terverifikasi otomatis & manual</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">check_circle</span>
            </div>
        </div>

        <!-- Ditolak / Revisi -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Ditolak / Revisi</span>
                <span class="text-3xl font-extrabold text-slate-900 mt-1">{{ $countDitolak }}</span>
                <span class="text-xs text-rose-600 font-semibold mt-0.5">Sesuai ketentuan kuota</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">cancel</span>
            </div>
        </div>

        <!-- SLA Approval -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Rata-rata Waktu Approval</span>
                <span class="text-3xl font-extrabold text-slate-900 mt-1">1.8 <span class="text-sm font-semibold text-slate-500">Jam</span></span>
                <span class="text-xs text-blue-600 font-semibold mt-0.5">SLA Pabrik Terpenuhi 96.2%</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">speed</span>
            </div>
        </div>
    </div>

    <!-- Toolbar Filters (Status Tabs & Type Filter Chips) -->
    <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Status Tabs (Semua, Menunggu, Disetujui, Ditolak) -->
        <div class="flex items-center p-1 rounded-xl bg-slate-100 border border-slate-200 text-xs font-semibold w-full md:w-auto">
            <button 
                wire:click="$set('filterStatus', 'SEMUA')"
                type="button" 
                class="px-3.5 py-1.5 rounded-lg transition-colors {{ $filterStatus === 'SEMUA' ? 'bg-[#0F4C81] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
            >
                Semua
            </button>
            <button 
                wire:click="$set('filterStatus', 'MENUNGGU')"
                type="button" 
                class="px-3.5 py-1.5 rounded-lg transition-colors {{ $filterStatus === 'MENUNGGU' ? 'bg-[#0F4C81] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
            >
                Menunggu ({{ $countMenunggu }})
            </button>
            <button 
                wire:click="$set('filterStatus', 'DISETUJUI')"
                type="button" 
                class="px-3.5 py-1.5 rounded-lg transition-colors {{ $filterStatus === 'DISETUJUI' ? 'bg-[#0F4C81] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
            >
                Disetujui ({{ $countDisetujui }})
            </button>
            <button 
                wire:click="$set('filterStatus', 'DITOLAK')"
                type="button" 
                class="px-3.5 py-1.5 rounded-lg transition-colors {{ $filterStatus === 'DITOLAK' ? 'bg-[#0F4C81] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
            >
                Ditolak ({{ $countDitolak }})
            </button>
        </div>

        <!-- Type Filter Chips -->
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
            <button 
                wire:click="$set('filterTipe', 'SEMUA')"
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $filterTipe === 'SEMUA' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                Semua Jenis
            </button>
            <button 
                wire:click="$set('filterTipe', 'TUKAR_SHIFT')"
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $filterTipe === 'TUKAR_SHIFT' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100' }}"
            >
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                <span>Tukar Shift</span>
            </button>
            <button 
                wire:click="$set('filterTipe', 'LEMBUR')"
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $filterTipe === 'LEMBUR' ? 'bg-purple-600 text-white shadow-xs' : 'bg-purple-50 text-purple-800 border border-purple-200 hover:bg-purple-100' }}"
            >
                <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                <span>SPKL Lembur</span>
            </button>
            <button 
                wire:click="$set('filterTipe', 'CUTI_IZIN')"
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $filterTipe === 'CUTI_IZIN' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-800 border border-blue-200 hover:bg-blue-100' }}"
            >
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                <span>Cuti &amp; Izin</span>
            </button>
        </div>
    </div>

    <!-- Main Two-Column View (List on Left, Detail on Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN: Applications List (7-8 cols) -->
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-3">
            
            <!-- Tukar Shift Items -->
            @foreach ($tukarList as $tukar)
                <div 
                    wire:click="selectItem('TUKAR_SHIFT', {{ $tukar->id_tukar }})"
                    class="p-4 rounded-xl border bg-white shadow-xs hover:border-[#0F4C81] transition-all cursor-pointer flex items-start justify-between gap-4 {{ ($selectedType === 'TUKAR_SHIFT' && $selectedId === $tukar->id_tukar) ? 'border-2 border-[#0F4C81] ring-2 ring-blue-100' : 'border-slate-200' }}"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-900 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            {{ substr($tukar->pemohon->nama_karyawan, 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-slate-900 text-sm leading-tight">
                                    {{ $tukar->pemohon->nama_karyawan }} &harr; {{ $tukar->pengganti->nama_karyawan }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Tukar Shift
                                </span>
                            </div>
                            <span class="text-xs text-slate-500 mt-1">
                                Dept: {{ $tukar->pemohon->departemen->kode_departemen }} &bull; Tanggal: 
                                <span class="font-semibold text-slate-800">{{ $tukar->jadwalPemohon->tanggal->format('d M Y') }}</span> 
                                ({{ $tukar->jadwalPemohon->kode_shift }}) &harr; 
                                <span class="font-semibold text-slate-800">{{ $tukar->jadwalPengganti->tanggal->format('d M Y') }}</span> 
                                ({{ $tukar->jadwalPengganti->kode_shift }})
                            </span>
                            <span class="text-xs text-slate-600 mt-1 italic">
                                "{{ Str::limit($tukar->alasan, 60) }}"
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end shrink-0 gap-1.5">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                            @if($tukar->status === 'DISETUJUI') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif($tukar->status === 'DITOLAK') bg-rose-50 text-rose-700 border border-rose-200
                            @elseif($tukar->status === 'MENUNGGU_PENGGANTI') bg-amber-50 text-amber-800 border border-amber-200
                            @else bg-blue-50 text-blue-700 border border-blue-200 @endif
                        ">
                            {{ str_replace('_', ' ', $tukar->status) }}
                        </span>
                        <span class="text-[10px] text-slate-400">
                            {{ $tukar->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @endforeach

            <!-- Lembur Items -->
            @foreach ($lemburList as $lembur)
                <div 
                    wire:click="selectItem('LEMBUR', {{ $lembur->id_lembur }})"
                    class="p-4 rounded-xl border bg-white shadow-xs hover:border-[#0F4C81] transition-all cursor-pointer flex items-start justify-between gap-4 {{ ($selectedType === 'LEMBUR' && $selectedId === $lembur->id_lembur) ? 'border-2 border-[#0F4C81] ring-2 ring-blue-100' : 'border-slate-200' }}"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-900 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            {{ substr($lembur->karyawan->nama_karyawan, 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-slate-900 text-sm leading-tight">
                                    {{ $lembur->karyawan->nama_karyawan }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-200">
                                    SPKL Lembur ({{ round($lembur->menit_lembur / 60, 1) }} Jam)
                                </span>
                            </div>
                            <span class="text-xs text-slate-500 mt-1">
                                Dept: {{ $lembur->karyawan->departemen->kode_departemen }} &bull; Tanggal: 
                                <span class="font-semibold text-slate-800">{{ $lembur->tanggal->format('d M Y') }}</span> 
                                ({{ substr($lembur->jam_mulai, 0, 5) }} - {{ substr($lembur->jam_selesai, 0, 5) }})
                            </span>
                            <span class="text-xs text-slate-600 mt-1 italic">
                                "{{ Str::limit($lembur->alasan, 60) }}"
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end shrink-0 gap-1.5">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                            @if($lembur->status === 'DISETUJUI') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif($lembur->status === 'DITOLAK') bg-rose-50 text-rose-700 border border-rose-200
                            @else bg-amber-50 text-amber-800 border border-amber-200 @endif
                        ">
                            {{ $lembur->status }}
                        </span>
                        <span class="text-[10px] text-slate-400">
                            {{ $lembur->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @endforeach

            <!-- Cuti / Izin Items -->
            @foreach ($pengajuanList as $p)
                <div 
                    wire:click="selectItem('PENGAJUAN', {{ $p->id_pengajuan }})"
                    class="p-4 rounded-xl border bg-white shadow-xs hover:border-[#0F4C81] transition-all cursor-pointer flex items-start justify-between gap-4 {{ ($selectedType === 'PENGAJUAN' && $selectedId === $p->id_pengajuan) ? 'border-2 border-[#0F4C81] ring-2 ring-blue-100' : 'border-slate-200' }}"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-900 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                            {{ substr($p->karyawan->nama_karyawan, 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-slate-900 text-sm leading-tight">
                                    {{ $p->karyawan->nama_karyawan }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                    {{ $p->jenis }}
                                </span>
                            </div>
                            <span class="text-xs text-slate-500 mt-1">
                                Dept: {{ $p->karyawan->departemen->kode_departemen }} &bull; Periode: 
                                <span class="font-semibold text-slate-800">{{ $p->tgl_mulai->format('d M Y') }} s.d {{ $p->tgl_selesai->format('d M Y') }}</span>
                            </span>
                            <span class="text-xs text-slate-600 mt-1 italic">
                                "{{ Str::limit($p->alasan, 60) }}"
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end shrink-0 gap-1.5">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold
                            @if($p->status === 'DISETUJUI') bg-emerald-50 text-emerald-700 border border-emerald-200
                            @elseif($p->status === 'DITOLAK') bg-rose-50 text-rose-700 border border-rose-200
                            @else bg-amber-50 text-amber-800 border border-amber-200 @endif
                        ">
                            {{ $p->status }}
                        </span>
                        <span class="text-[10px] text-slate-400">
                            {{ $p->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @endforeach

            @if ($tukarList->isEmpty() && $lemburList->isEmpty() && $pengajuanList->isEmpty())
                <div class="p-12 rounded-xl bg-white border border-slate-200 text-center text-slate-500">
                    <span class="material-symbols-outlined text-[48px] text-slate-300">inbox</span>
                    <p class="text-base font-semibold mt-2">Tidak ada permohonan dalam antrean</p>
                    <p class="text-xs text-slate-400 mt-0.5">Semua berkas pengajuan telah diproses atau filter tidak sesuai.</p>
                </div>
            @endif
        </div>

        <!-- RIGHT COLUMN: Detail & Action Panel (4-5 cols) -->
        <div class="lg:col-span-5 xl:col-span-4 flex flex-col gap-4 sticky top-20">
            @if ($detailItem)
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5 md:p-6 flex flex-col gap-5">
                    <!-- Detail Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Detail Permohonan</span>
                            <span class="text-base font-bold text-slate-900 mt-0.5">
                                @if($selectedType === 'TUKAR_SHIFT') Pertukaran Shift
                                @elseif($selectedType === 'LEMBUR') SPKL Lembur
                                @else Permohonan {{ $detailItem->jenis }} @endif
                            </span>
                        </div>
                        <button wire:click="$set('selectedItem', null)" class="text-slate-400 hover:text-slate-600">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>

                    <!-- Applicant Profile -->
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-full bg-[#0F4C81] text-white flex items-center justify-center font-bold text-sm shrink-0">
                            {{ substr($selectedType === 'TUKAR_SHIFT' ? $detailItem->pemohon->nama_karyawan : $detailItem->karyawan->nama_karyawan, 0, 1) }}
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-bold text-slate-900 text-sm truncate">
                                {{ $selectedType === 'TUKAR_SHIFT' ? $detailItem->pemohon->nama_karyawan : $detailItem->karyawan->nama_karyawan }}
                            </span>
                            <span class="text-xs text-slate-500">
                                {{ $selectedType === 'TUKAR_SHIFT' ? $detailItem->pemohon->id_karyawan : $detailItem->karyawan->id_karyawan }} &bull; 
                                {{ $selectedType === 'TUKAR_SHIFT' ? $detailItem->pemohon->departemen->nama_departemen : $detailItem->karyawan->departemen->nama_departemen }}
                            </span>
                        </div>
                    </div>

                    <!-- Detail Specifics -->
                    @if ($selectedType === 'TUKAR_SHIFT')
                        <div class="flex flex-col gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 flex flex-col gap-2">
                                <span class="font-bold text-amber-900">Rincian Pertukaran Shift:</span>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600">{{ $detailItem->pemohon->nama_karyawan }}:</span>
                                    <span class="font-bold text-slate-900">{{ $detailItem->jadwalPemohon->tanggal->format('d-m-Y') }} (Shift {{ $detailItem->jadwalPemohon->kode_shift }})</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600">{{ $detailItem->pengganti->nama_karyawan }}:</span>
                                    <span class="font-bold text-slate-900">{{ $detailItem->jadwalPengganti->tanggal->format('d-m-Y') }} (Shift {{ $detailItem->jadwalPengganti->kode_shift }})</span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="font-bold text-slate-700">Alasan Permohonan:</span>
                                <p class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-800 leading-relaxed italic">
                                    "{{ $detailItem->alasan }}"
                                </p>
                            </div>

                            <div class="flex items-center gap-2 p-2.5 rounded-lg bg-emerald-50 text-emerald-800 text-[11px] font-medium border border-emerald-200">
                                <span class="material-symbols-outlined text-[16px] text-emerald-600">verified</span>
                                <span>Kedua operator satu departemen &amp; jeda istirahat &gt; 8 jam (Sesuai BR-10).</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if (Auth::user()->id_karyawan === $detailItem->id_pengganti && $detailItem->status === 'MENUNGGU_PENGGANTI')
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                <button 
                                    wire:click="responTukarPengganti({{ $detailItem->id_tukar }}, false)"
                                    type="button" 
                                    class="w-1/2 h-10 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 font-bold text-xs transition-colors cursor-pointer"
                                >
                                    Tolak
                                </button>
                                <button 
                                    wire:click="responTukarPengganti({{ $detailItem->id_tukar }}, true)"
                                    type="button" 
                                    class="w-1/2 h-10 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-sm cursor-pointer"
                                >
                                    Setujui Pertukaran
                                </button>
                            </div>
                        @elseif (!Auth::user()->isOperator() && $detailItem->status === 'MENUNGGU_SUPERVISOR')
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                <button 
                                    wire:click="approveTukarSupervisor({{ $detailItem->id_tukar }}, false)"
                                    type="button" 
                                    class="w-1/2 h-10 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 font-bold text-xs transition-colors cursor-pointer"
                                >
                                    Tolak Berkas
                                </button>
                                <button 
                                    wire:click="approveTukarSupervisor({{ $detailItem->id_tukar }}, true)"
                                    type="button" 
                                    class="w-1/2 h-10 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-bold text-xs transition-colors shadow-sm cursor-pointer"
                                >
                                    Setuju &amp; Teruskan
                                </button>
                            </div>
                        @endif

                    @elseif ($selectedType === 'LEMBUR')
                        <div class="flex flex-col gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-purple-50 border border-purple-200 flex flex-col gap-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600">Tanggal Lembur:</span>
                                    <span class="font-bold text-slate-900">{{ $detailItem->tanggal->format('d-m-Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600">Jam Mulai - Selesai:</span>
                                    <span class="font-bold text-slate-900">{{ substr($detailItem->jam_mulai, 0, 5) }} - {{ substr($detailItem->jam_selesai, 0, 5) }} WIB</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600">Durasi Lembur:</span>
                                    <span class="font-extrabold text-purple-900">{{ round($detailItem->menit_lembur / 60, 1) }} Jam ({{ $detailItem->menit_lembur }} Menit)</span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="font-bold text-slate-700">Alasan Lembur:</span>
                                <p class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-800 leading-relaxed italic">
                                    "{{ $detailItem->alasan }}"
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if (!Auth::user()->isOperator() && $detailItem->status === 'DIAJUKAN')
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                <button 
                                    wire:click="approveLemburSupervisor({{ $detailItem->id_lembur }}, false)"
                                    type="button" 
                                    class="w-1/2 h-10 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 font-bold text-xs transition-colors cursor-pointer"
                                >
                                    Tolak Lembur
                                </button>
                                <button 
                                    wire:click="approveLemburSupervisor({{ $detailItem->id_lembur }}, true)"
                                    type="button" 
                                    class="w-1/2 h-10 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-bold text-xs transition-colors shadow-sm cursor-pointer"
                                >
                                    Setujui Lembur
                                </button>
                            </div>
                        @endif

                    @endif
                </div>
            @else
                <div class="p-8 rounded-xl bg-white border border-slate-200 shadow-sm text-center text-slate-400">
                    <span class="material-symbols-outlined text-[36px] text-slate-300">touch_app</span>
                    <p class="text-sm font-semibold text-slate-600 mt-2">Pilih berkas di samping</p>
                    <p class="text-xs text-slate-400 mt-0.5">Klik salah satu permohonan untuk melihat detail dan melakukan verifikasi.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL AJUKAN TUKAR SHIFT -->
    @if ($isTukarShiftModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
                <div class="h-16 px-6 border-b border-slate-200 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600 text-[24px]">sync_alt</span>
                        <h3 class="font-bold text-slate-900 text-base">Ajukan Tukar Shift Operator</h3>
                    </div>
                    <button wire:click="$set('isTukarShiftModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="submitTukarShift" class="p-6 flex flex-col gap-4 text-xs">
                    <!-- Pilih Jadwal Sendiri -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700">1. Pilih Jadwal Anda yang Ingin Ditukar *</label>
                        <select wire:model.live="id_jadwal_pemohon" class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]">
                            <option value="">-- Pilih Jadwal Anda --</option>
                            @foreach ($mySchedules as $js)
                                <option value="{{ $js->id_jadwal }}">
                                    {{ $js->tanggal->format('d-m-Y') }} (Shift {{ $js->kode_shift }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_jadwal_pemohon') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pilih Rekan Kerja Pengganti -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700">2. Pilih Rekan Pengganti (Satu Departemen) *</label>
                        <select wire:model.live="id_pengganti" class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]">
                            <option value="">-- Pilih Rekan Satu Departemen --</option>
                            @foreach ($colleagues as $c)
                                <option value="{{ $c->id_karyawan }}">{{ $c->nama_karyawan }} ({{ $c->id_karyawan }})</option>
                            @endforeach
                        </select>
                        @error('id_pengganti') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pilih Jadwal Rekan Pengganti -->
                    @if ($id_pengganti)
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-slate-700">3. Pilih Jadwal Rekan yang Ditukar *</label>
                            <select wire:model="id_jadwal_pengganti" class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]">
                                <option value="">-- Pilih Jadwal Rekan --</option>
                                @foreach ($partnerSchedules as $pjs)
                                    <option value="{{ $pjs->id_jadwal }}">
                                        {{ $pjs->tanggal->format('d-m-Y') }} (Shift {{ $pjs->kode_shift }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_jadwal_pengganti') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <!-- Alasan Tukar -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700">4. Alasan Pertukaran Shift *</label>
                        <textarea 
                            wire:model="alasan_tukar" 
                            rows="3" 
                            placeholder="Jelaskan keperluan pertukaran shift..." 
                            class="p-3 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-[#0F4C81]"
                        ></textarea>
                        @error('alasan_tukar') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="p-3 rounded-lg bg-blue-50 text-blue-800 text-[11px] leading-relaxed border border-blue-200">
                        <b>Catatan Regulasi:</b> Sistem otomatis memeriksa ketiadaan jadwal ganda dan batas istirahat minimal 8 jam antar shift.
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                        <button 
                            wire:click="$set('isTukarShiftModalOpen', false)" 
                            type="button" 
                            class="h-10 px-4 rounded-xl bg-white border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="h-10 px-5 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-semibold text-xs shadow-sm transition-all"
                        >
                            Kirim Permohonan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL AJUKAN LEMBUR -->
    @if ($isLemburModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs">
            <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col">
                <div class="h-16 px-6 border-b border-slate-200 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-purple-600 text-[24px]">more_time</span>
                        <h3 class="font-bold text-slate-900 text-base">Form Pengajuan Lembur (SPKL)</h3>
                    </div>
                    <button wire:click="$set('isLemburModalOpen', false)" class="text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="submitLembur" class="p-6 flex flex-col gap-4 text-xs">
                    <!-- Tanggal Lembur -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700">Tanggal Pelaksanaan Lembur *</label>
                        <input 
                            wire:model="tgl_lembur" 
                            type="date" 
                            class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]"
                        >
                        @error('tgl_lembur') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jam Mulai & Jam Selesai -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-slate-700">Jam Mulai *</label>
                            <input 
                                wire:model="jam_mulai_lembur" 
                                type="time" 
                                class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]"
                            >
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-bold text-slate-700">Jam Selesai *</label>
                            <input 
                                wire:model="jam_selesai_lembur" 
                                type="time" 
                                class="h-10 px-3 rounded-lg border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#0F4C81]"
                            >
                        </div>
                    </div>

                    <!-- Alasan Lembur -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-slate-700">Tugas / Alasan Lembur *</label>
                        <textarea 
                            wire:model="alasan_lembur" 
                            rows="3" 
                            placeholder="Contoh: Kejar target backlog lini stamping, perbaikan mesin utilitas..." 
                            class="p-3 rounded-lg border border-slate-300 text-xs focus:ring-2 focus:ring-[#0F4C81]"
                        ></textarea>
                        @error('alasan_lembur') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                        <button 
                            wire:click="$set('isLemburModalOpen', false)" 
                            type="button" 
                            class="h-10 px-4 rounded-xl bg-white border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="h-10 px-5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold text-xs shadow-sm transition-all"
                        >
                            Ajukan Lembur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
