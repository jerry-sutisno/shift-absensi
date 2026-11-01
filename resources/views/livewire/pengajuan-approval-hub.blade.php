<div>
    <!-- Top Action Hub -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div class="flex flex-col gap-0.5">
            <h1 class="text-xl font-semibold text-slate-900">Pengajuan &amp; Approval</h1>
            <p class="text-xs text-slate-500 max-w-3xl">
                Pengelolaan dan verifikasi permohonan izin, cuti, pertukaran jadwal shift, dan lembur operator.
            </p>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
            <button 
                wire:click="openTukarShiftModal"
                type="button" 
                class="h-9 px-3.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">sync_alt</span>
                <span>Ajukan Tukar Shift</span>
            </button>
            <button 
                wire:click="openLemburModal"
                type="button" 
                class="h-9 px-3.5 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-medium text-xs transition-colors duration-150 flex items-center gap-1.5 cursor-pointer"
            >
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Ajukan Lembur</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Metrics Cards (Clean minimal metrics, no circular colored icon containers) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <!-- Menunggu Verifikasi -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Menunggu Verifikasi</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ $countMenunggu }}</span>
            <span class="text-xs text-amber-600 font-medium mt-1">Butuh tindakan approver</span>
        </div>

        <!-- Disetujui Hari Ini -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Disetujui Hari Ini</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ $countDisetujui }}</span>
            <span class="text-xs text-slate-500 mt-1">Telah diverifikasi</span>
        </div>

        <!-- Ditolak / Revisi -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Ditolak</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">{{ $countDitolak }}</span>
            <span class="text-xs text-slate-500 mt-1">Sesuai evaluasi kuota</span>
        </div>

        <!-- Rata-rata Waktu Approval -->
        <div class="p-5 rounded-xl bg-white border border-slate-200 flex flex-col justify-between">
            <span class="text-xs font-medium text-slate-500">Rata-rata Waktu Approval</span>
            <span class="text-2xl font-semibold text-slate-900 mt-2">1.8 Jam</span>
            <span class="text-xs text-slate-500 mt-1">Tingkat respons operasional</span>
        </div>
    </div>

    <!-- Toolbar Filters (Status Tabs & Type Filter Chips) -->
    <div class="p-3.5 rounded-xl bg-white border border-slate-200 mb-6 flex flex-col md:flex-row items-center justify-between gap-3">
        <!-- Status Tabs -->
        <div class="flex items-center p-1 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium w-full md:w-auto">
            <button 
                wire:click="$set('filterStatus', 'SEMUA')"
                type="button" 
                class="px-3 py-1.5 rounded-lg transition-colors duration-150 {{ $filterStatus === 'SEMUA' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:text-slate-900' }}"
            >
                Semua
            </button>
            <button 
                wire:click="$set('filterStatus', 'MENUNGGU')"
                type="button" 
                class="px-3 py-1.5 rounded-lg transition-colors duration-150 {{ $filterStatus === 'MENUNGGU' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:text-slate-900' }}"
            >
                Menunggu ({{ $countMenunggu }})
            </button>
            <button 
                wire:click="$set('filterStatus', 'DISETUJUI')"
                type="button" 
                class="px-3 py-1.5 rounded-lg transition-colors duration-150 {{ $filterStatus === 'DISETUJUI' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:text-slate-900' }}"
            >
                Disetujui ({{ $countDisetujui }})
            </button>
            <button 
                wire:click="$set('filterStatus', 'DITOLAK')"
                type="button" 
                class="px-3 py-1.5 rounded-lg transition-colors duration-150 {{ $filterStatus === 'DITOLAK' ? 'bg-[#0F4C81] text-white' : 'text-slate-600 hover:text-slate-900' }}"
            >
                Ditolak ({{ $countDitolak }})
            </button>
        </div>

        <!-- Type Filter Chips -->
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
            <button 
                wire:click="$set('filterTipe', 'SEMUA')"
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-150 {{ $filterTipe === 'SEMUA' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                Semua Jenis
            </button>
            <button 
                wire:click="$set('filterTipe', 'TUKAR_SHIFT')"
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-150 {{ $filterTipe === 'TUKAR_SHIFT' ? 'bg-[#0F4C81] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                Tukar Shift
            </button>
            <button 
                wire:click="$set('filterTipe', 'LEMBUR')"
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-150 {{ $filterTipe === 'LEMBUR' ? 'bg-[#0F4C81] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                Lembur
            </button>
            <button 
                wire:click="$set('filterTipe', 'CUTI_IZIN')"
                type="button"
                class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors duration-150 {{ $filterTipe === 'CUTI_IZIN' ? 'bg-[#0F4C81] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                Cuti &amp; Izin
            </button>
        @if (Auth::user()->isAdminHr() || Auth::user()->isDirektur())
            <div class="w-full md:w-auto">
                <select 
                    wire:model.live="filterDepartemen" 
                    class="h-8 px-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-800"
                >
                    <option value="">Semua Departemen</option>
                    @foreach($departemens as $dept)
                        <option value="{{ $dept->id_departemen }}">{{ $dept->kode_departemen }} - {{ $dept->nama_departemen }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        </div>
    </div>

    <!-- Main Two-Column View -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN: Applications List -->
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-2.5">
            
            <!-- Tukar Shift Items -->
            @foreach ($tukarList as $tukar)
                <div 
                    wire:click="selectItem('TUKAR_SHIFT', {{ $tukar->id_tukar }})"
                    class="p-4 rounded-xl border bg-white hover:border-slate-300 transition-colors duration-150 cursor-pointer flex items-start justify-between gap-4 {{ ($selectedType === 'TUKAR_SHIFT' && $selectedId === $tukar->id_tukar) ? 'border-[#0F4C81] ring-1 ring-[#0F4C81]' : 'border-slate-200' }}"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-medium text-xs flex items-center justify-center shrink-0 mt-0.5">
                            {{ substr($tukar->pemohon->nama_karyawan, 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-medium text-slate-900 text-xs leading-tight">
                                    {{ $tukar->pemohon->nama_karyawan }} &harr; {{ $tukar->pengganti->nama_karyawan }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                    Tukar Shift
                                </span>
                            </div>
                            <span class="text-xs text-slate-500 mt-1">
                                Dept: {{ $tukar->pemohon->departemen->kode_departemen }} &bull; 
                                {{ $tukar->jadwalPemohon->tanggal->format('d-m-Y') }} ({{ $tukar->jadwalPemohon->kode_shift }}) &harr; 
                                {{ $tukar->jadwalPengganti->tanggal->format('d-m-Y') }} ({{ $tukar->jadwalPengganti->kode_shift }})
                            </span>
                            <span class="text-xs text-slate-600 mt-1 italic">
                                "{{ Str::limit($tukar->alasan, 60) }}"
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end shrink-0 gap-1">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium
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
                    class="p-4 rounded-xl border bg-white hover:border-slate-300 transition-colors duration-150 cursor-pointer flex items-start justify-between gap-4 {{ ($selectedType === 'LEMBUR' && $selectedId === $lembur->id_lembur) ? 'border-[#0F4C81] ring-1 ring-[#0F4C81]' : 'border-slate-200' }}"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-medium text-xs flex items-center justify-center shrink-0 mt-0.5">
                            {{ substr($lembur->karyawan->nama_karyawan, 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-medium text-slate-900 text-xs leading-tight">
                                    {{ $lembur->karyawan->nama_karyawan }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-50 text-purple-800 border border-purple-200">
                                    Lembur ({{ round($lembur->menit_lembur / 60, 1) }} Jam)
                                </span>
                            </div>
                            <span class="text-xs text-slate-500 mt-1">
                                Dept: {{ $lembur->karyawan->departemen->kode_departemen }} &bull; Tanggal: 
                                {{ $lembur->tanggal->format('d-m-Y') }} ({{ substr($lembur->jam_mulai, 0, 5) }} - {{ substr($lembur->jam_selesai, 0, 5) }})
                            </span>
                            <span class="text-xs text-slate-600 mt-1 italic">
                                "{{ Str::limit($lembur->alasan, 60) }}"
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end shrink-0 gap-1">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium
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
                    class="p-4 rounded-xl border bg-white hover:border-slate-300 transition-colors duration-150 cursor-pointer flex items-start justify-between gap-4 {{ ($selectedType === 'PENGAJUAN' && $selectedId === $p->id_pengajuan) ? 'border-[#0F4C81] ring-1 ring-[#0F4C81]' : 'border-slate-200' }}"
                >
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-medium text-xs flex items-center justify-center shrink-0 mt-0.5">
                            {{ substr($p->karyawan->nama_karyawan, 0, 1) }}
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-medium text-slate-900 text-xs leading-tight">
                                    {{ $p->karyawan->nama_karyawan }}
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-800 border border-blue-200">
                                    {{ $p->jenis }}
                                </span>
                            </div>
                            <span class="text-xs text-slate-500 mt-1">
                                Dept: {{ $p->karyawan->departemen->kode_departemen }} &bull; Periode: 
                                {{ $p->tgl_mulai->format('d-m-Y') }} s/d {{ $p->tgl_selesai->format('d-m-Y') }}
                            </span>
                            <span class="text-xs text-slate-600 mt-1 italic">
                                "{{ Str::limit($p->alasan, 60) }}"
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end shrink-0 gap-1">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-medium
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
                <div class="p-8 rounded-xl bg-white border border-slate-200 text-center text-xs text-slate-500">
                    Tidak ada permohonan dalam antrean saat ini.
                </div>
            @endif
        </div>

        <!-- RIGHT COLUMN: Detail & Action Panel -->
        <div class="lg:col-span-5 xl:col-span-4 flex flex-col gap-4 sticky top-20">
            @if ($detailItem)
                <div class="rounded-xl border border-slate-200 bg-white p-5 md:p-6 flex flex-col gap-4">
                    <!-- Detail Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-medium text-slate-400">Detail Permohonan</span>
                            <h2 class="text-sm font-semibold text-slate-900 mt-0.5">
                                @if($selectedType === 'TUKAR_SHIFT') Pertukaran Shift
                                @elseif($selectedType === 'LEMBUR') SPKL Lembur
                                @else Permohonan {{ $detailItem->jenis }} @endif
                            </h2>
                        </div>
                        <button wire:click="$set('selectedItem', null)" class="text-slate-400 hover:text-slate-600 p-1">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                        </button>
                    </div>

                    <!-- Applicant Profile -->
                    <div class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-8 h-8 rounded-full bg-[#0F4C81] text-white flex items-center justify-center font-medium text-xs shrink-0">
                            {{ substr($selectedType === 'TUKAR_SHIFT' ? $detailItem->pemohon->nama_karyawan : $detailItem->karyawan->nama_karyawan, 0, 1) }}
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-medium text-slate-900 text-xs truncate">
                                {{ $selectedType === 'TUKAR_SHIFT' ? $detailItem->pemohon->nama_karyawan : $detailItem->karyawan->nama_karyawan }}
                            </span>
                            <span class="text-[11px] text-slate-400">
                                {{ $selectedType === 'TUKAR_SHIFT' ? $detailItem->pemohon->id_karyawan : $detailItem->karyawan->id_karyawan }} &bull; 
                                {{ $selectedType === 'TUKAR_SHIFT' ? $detailItem->pemohon->departemen->nama_departemen : $detailItem->karyawan->departemen->nama_departemen }}
                            </span>
                        </div>
                    </div>

                    <!-- Detail Specifics -->
                    @if ($selectedType === 'TUKAR_SHIFT')
                        <div class="flex flex-col gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-2">
                                <span class="font-medium text-slate-800">Rincian Pertukaran:</span>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>{{ $detailItem->pemohon->nama_karyawan }}:</span>
                                    <span class="font-medium text-slate-900">{{ $detailItem->jadwalPemohon->tanggal->format('d-m-Y') }} (Shift {{ $detailItem->jadwalPemohon->kode_shift }})</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>{{ $detailItem->pengganti->nama_karyawan }}:</span>
                                    <span class="font-medium text-slate-900">{{ $detailItem->jadwalPengganti->tanggal->format('d-m-Y') }} (Shift {{ $detailItem->jadwalPengganti->kode_shift }})</span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium text-slate-700">Alasan:</span>
                                <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 leading-relaxed text-xs">
                                    "{{ $detailItem->alasan }}"
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if (Auth::user()->id_karyawan === $detailItem->id_pengganti && $detailItem->status === 'MENUNGGU_PENGGANTI')
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                <button 
                                    wire:click="responTukarPengganti({{ $detailItem->id_tukar }}, false)"
                                    type="button" 
                                    class="w-1/2 h-9 rounded-xl bg-white text-rose-700 border border-rose-200 hover:bg-rose-50 font-medium text-xs transition-colors duration-150 cursor-pointer"
                                >
                                    Tolak
                                </button>
                                <button 
                                    wire:click="responTukarPengganti({{ $detailItem->id_tukar }}, true)"
                                    type="button" 
                                    class="w-1/2 h-9 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs transition-colors duration-150 cursor-pointer"
                                >
                                    Setujui
                                </button>
                            </div>
                        @elseif (!Auth::user()->isOperator() && !Auth::user()->isAdminHr() && !Auth::user()->isDirektur() && $detailItem->status === 'MENUNGGU_SUPERVISOR')
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                <button 
                                    wire:click="approveTukarSupervisor({{ $detailItem->id_tukar }}, false)"
                                    type="button" 
                                    class="w-1/2 h-9 rounded-xl bg-white text-rose-700 border border-rose-200 hover:bg-rose-50 font-medium text-xs transition-colors duration-150 cursor-pointer"
                                >
                                    Tolak
                                </button>
                                <button 
                                    wire:click="approveTukarSupervisor({{ $detailItem->id_tukar }}, true)"
                                    type="button" 
                                    class="w-1/2 h-9 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-medium text-xs transition-colors duration-150 cursor-pointer"
                                >
                                    Setujui
                                </button>
                            </div>
                        @endif

                    @elseif ($selectedType === 'LEMBUR')
                        <div class="flex flex-col gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-2">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Tanggal Lembur:</span>
                                    <span class="font-medium text-slate-900">{{ $detailItem->tanggal->format('d-m-Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Jam:</span>
                                    <span class="font-medium text-slate-900">{{ substr($detailItem->jam_mulai, 0, 5) }} - {{ substr($detailItem->jam_selesai, 0, 5) }} WIB</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Durasi:</span>
                                    <span class="font-medium text-slate-900">{{ round($detailItem->menit_lembur / 60, 1) }} Jam</span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium text-slate-700">Tugas / Alasan:</span>
                                <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 leading-relaxed text-xs">
                                    "{{ $detailItem->alasan }}"
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @if (!Auth::user()->isOperator() && !Auth::user()->isAdminHr() && !Auth::user()->isDirektur() && $detailItem->status === 'DIAJUKAN')
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                <button 
                                    wire:click="approveLemburSupervisor({{ $detailItem->id_lembur }}, false)"
                                    type="button" 
                                    class="w-1/2 h-9 rounded-xl bg-white text-rose-700 border border-rose-200 hover:bg-rose-50 font-medium text-xs transition-colors duration-150 cursor-pointer"
                                >
                                    Tolak
                                </button>
                                <button 
                                    wire:click="approveLemburSupervisor({{ $detailItem->id_lembur }}, true)"
                                    type="button" 
                                    class="w-1/2 h-9 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-medium text-xs transition-colors duration-150 cursor-pointer"
                                >
                                    Setujui
                                </button>
                            </div>
                        @endif

                    @elseif ($selectedType === 'PENGAJUAN')
                        <div class="flex flex-col gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-2">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Jenis Permohonan:</span>
                                    <span class="font-medium text-slate-900">{{ $detailItem->jenis }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Periode:</span>
                                    <span class="font-medium text-slate-900">{{ $detailItem->tgl_mulai->format('d-m-Y') }} s/d {{ $detailItem->tgl_selesai->format('d-m-Y') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Tahap Approval:</span>
                                    <span class="font-medium text-slate-900">{{ $detailItem->tahap }}</span>
                                </div>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium text-slate-700">Alasan:</span>
                                <p class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 leading-relaxed text-xs">
                                    "{{ $detailItem->alasan }}"
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        @can('approve', $detailItem)
                            @if ($detailItem->status === 'DIAJUKAN' && !Auth::user()->isAdminHr())
                                <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                    <button 
                                        wire:click="approvePengajuan({{ $detailItem->id_pengajuan }}, false)"
                                        type="button" 
                                        class="w-1/2 h-9 rounded-xl bg-white text-rose-700 border border-rose-200 hover:bg-rose-50 font-medium text-xs transition-colors duration-150 cursor-pointer"
                                    >
                                        Tolak
                                    </button>
                                    <button 
                                        wire:click="approvePengajuan({{ $detailItem->id_pengajuan }}, true)"
                                        type="button" 
                                        class="w-1/2 h-9 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-medium text-xs transition-colors duration-150 cursor-pointer"
                                    >
                                        Setujui
                                    </button>
                                </div>
                            @endif
                        @endcan

                    @endif
                </div>
            @else
                <div class="p-6 rounded-xl bg-white border border-slate-200 text-center text-xs text-slate-400">
                    Pilih salah satu permohonan di samping untuk melihat rincian dan melakukan verifikasi.
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL AJUKAN TUKAR SHIFT -->
    @if ($isTukarShiftModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
            <div class="w-full max-w-lg bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden flex flex-col">
                <div class="h-14 px-6 border-b border-slate-200 flex items-center justify-between bg-white">
                    <h3 class="font-medium text-slate-900 text-sm">Ajukan Tukar Shift Operator</h3>
                    <button wire:click="$set('isTukarShiftModalOpen', false)" class="text-slate-400 hover:text-slate-600 p-1">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="submitTukarShift" class="p-6 flex flex-col gap-4 text-xs">
                    <!-- Pilih Jadwal Sendiri -->
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">1. Jadwal Anda yang Ditukar <span class="text-rose-500">*</span></label>
                        <select wire:model.live="id_jadwal_pemohon" class="h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150">
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
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">2. Rekan Pengganti (Satu Departemen) <span class="text-rose-500">*</span></label>
                        <select wire:model.live="id_pengganti" class="h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150">
                            <option value="">-- Pilih Rekan Satu Departemen --</option>
                            @foreach ($colleagues as $c)
                                <option value="{{ $c->id_karyawan }}">{{ $c->nama_karyawan }} ({{ $c->id_karyawan }})</option>
                            @endforeach
                        </select>
                        @error('id_pengganti') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Pilih Jadwal Rekan Pengganti -->
                    @if ($id_pengganti)
                        <div class="flex flex-col gap-1">
                            <label class="font-medium text-slate-700">3. Jadwal Rekan yang Ditukar <span class="text-rose-500">*</span></label>
                            <select wire:model="id_jadwal_pengganti" class="h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150">
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
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">4. Alasan Pertukaran <span class="text-rose-500">*</span></label>
                        <textarea 
                            wire:model="alasan_tukar" 
                            rows="3" 
                            placeholder="Tuliskan alasan pertukaran shift..." 
                            class="p-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150"
                        ></textarea>
                        @error('alasan_tukar') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                        <button 
                            wire:click="$set('isTukarShiftModalOpen', false)" 
                            type="button" 
                            class="h-9 px-3.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-medium text-xs hover:bg-slate-50 transition-colors duration-150"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="h-9 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-medium text-xs transition-colors duration-150"
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
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40">
            <div class="w-full max-w-lg bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden flex flex-col">
                <div class="h-14 px-6 border-b border-slate-200 flex items-center justify-between bg-white">
                    <h3 class="font-medium text-slate-900 text-sm">Pengajuan Lembur (SPKL)</h3>
                    <button wire:click="$set('isLemburModalOpen', false)" class="text-slate-400 hover:text-slate-600 p-1">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="submitLembur" class="p-6 flex flex-col gap-4 text-xs">
                    <!-- Tanggal Lembur -->
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">Tanggal Pelaksanaan Lembur <span class="text-rose-500">*</span></label>
                        <input 
                            wire:model="tgl_lembur" 
                            type="date" 
                            class="h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150"
                        >
                        @error('tgl_lembur') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jam Mulai & Jam Selesai -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex flex-col gap-1">
                            <label class="font-medium text-slate-700">Jam Mulai <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="jam_mulai_lembur" 
                                type="time" 
                                class="h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150"
                            >
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="font-medium text-slate-700">Jam Selesai <span class="text-rose-500">*</span></label>
                            <input 
                                wire:model="jam_selesai_lembur" 
                                type="time" 
                                class="h-10 px-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150"
                            >
                        </div>
                    </div>

                    <!-- Alasan Lembur -->
                    <div class="flex flex-col gap-1">
                        <label class="font-medium text-slate-700">Tugas / Alasan Lembur <span class="text-rose-500">*</span></label>
                        <textarea 
                            wire:model="alasan_lembur" 
                            rows="3" 
                            placeholder="Uraikan tugas lembur yang dilaksanakan..." 
                            class="p-3 rounded-xl bg-white border border-slate-200 text-xs text-slate-900 focus:outline-none focus:border-[#0F4C81] focus:ring-1 focus:ring-[#0F4C81] transition-colors duration-150"
                        ></textarea>
                        @error('alasan_lembur') <span class="text-rose-600 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200">
                        <button 
                            wire:click="$set('isLemburModalOpen', false)" 
                            type="button" 
                            class="h-9 px-3.5 rounded-xl bg-white border border-slate-200 text-slate-700 font-medium text-xs hover:bg-slate-50 transition-colors duration-150"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="h-9 px-4 rounded-xl bg-[#0F4C81] hover:bg-[#00355F] text-white font-medium text-xs transition-colors duration-150"
                        >
                            Ajukan Lembur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

