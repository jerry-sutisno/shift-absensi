<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\TukarShift;
use App\Models\Lembur;
use App\Models\Pengajuan;
use App\Models\Karyawan;
use App\Models\JadwalShift;
use App\Models\Departemen;
use App\Services\TukarShiftService;
use App\Services\OvertimeService;
use App\Services\PengajuanService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('layouts.app')]
#[Title('Pengajuan & Approval Operasional')]
class PengajuanApprovalHub extends Component
{
    use WithPagination;

    // Filters
    public string $filterStatus = 'MENUNGGU'; // SEMUA, MENUNGGU, DISETUJUI, DITOLAK
    public string $filterTipe = 'SEMUA'; // SEMUA, TUKAR_SHIFT, LEMBUR, CUTI_IZIN
    public string $search = '';
    public ?int $filterDepartemen = null;

    // Selected item for right detail panel
    public ?string $selectedType = null; // 'TUKAR_SHIFT', 'LEMBUR', 'PENGAJUAN'
    public ?int $selectedId = null;

    // Modals
    public bool $isTukarShiftModalOpen = false;
    public bool $isLemburModalOpen = false;

    // Form Tukar Shift
    public ?int $id_jadwal_pemohon = null;
    public ?string $id_pengganti = null;
    public ?int $id_jadwal_pengganti = null;
    public string $alasan_tukar = '';

    // Form Lembur
    public string $tgl_lembur = '';
    public string $jam_mulai_lembur = '16:00';
    public string $jam_selesai_lembur = '20:00';
    public string $alasan_lembur = '';

    public function mount(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && $user->karyawan && !$user->isAdminHr() && !$user->isDirektur()) {
            $this->filterDepartemen = $user->karyawan->id_departemen;
        } else {
            $this->filterDepartemen = null;
        }

        $this->tgl_lembur = now('Asia/Jakarta')->toDateString();
    }

    public function selectItem(string $type, int $id): void
    {
        if ($type === 'TUKAR_SHIFT') {
            $item = TukarShift::findOrFail($id);
            $this->authorize('view', $item);
        } elseif ($type === 'LEMBUR') {
            $item = Lembur::findOrFail($id);
            $this->authorize('view', $item);
        } elseif ($type === 'PENGAJUAN') {
            $item = Pengajuan::findOrFail($id);
            $this->authorize('view', $item);
        }

        $this->selectedType = $type;
        $this->selectedId = $id;
    }

    public function openTukarShiftModal(): void
    {
        $this->reset(['id_jadwal_pemohon', 'id_pengganti', 'id_jadwal_pengganti', 'alasan_tukar']);
        $this->isTukarShiftModalOpen = true;

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Auto-select first upcoming schedule of current user (must be PUBLISHED)
        $firstJadwal = JadwalShift::where('id_karyawan', $user->id_karyawan)
            ->where('status', 'PUBLISHED')
            ->where('tanggal', '>=', now('Asia/Jakarta')->toDateString())
            ->orderBy('tanggal')
            ->first();

        if ($firstJadwal) {
            $this->id_jadwal_pemohon = $firstJadwal->id_jadwal;
        }
    }

    public function openLemburModal(): void
    {
        $this->reset(['alasan_lembur']);
        $this->tgl_lembur = now('Asia/Jakarta')->toDateString();
        $this->isLemburModalOpen = true;
    }

    public function submitTukarShift(TukarShiftService $service): void
    {
        $this->validate([
            'id_jadwal_pemohon' => 'required|exists:jadwal_shift,id_jadwal',
            'id_pengganti' => 'required|exists:karyawan,id_karyawan',
            'id_jadwal_pengganti' => 'required|exists:jadwal_shift,id_jadwal',
            'alasan_tukar' => 'required|min:5',
        ], [
            'id_jadwal_pemohon.required' => 'Pilih jadwal Anda yang ingin ditukar.',
            'id_pengganti.required' => 'Pilih rekan kerja pengganti.',
            'id_jadwal_pengganti.required' => 'Pilih jadwal rekan pengganti.',
            'alasan_tukar.required' => 'Alasan pertukaran shift wajib diisi.',
        ]);

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $pemohon = $user->karyawan;
            $service->ajukanTukar(
                $pemohon,
                $this->id_pengganti,
                (int) $this->id_jadwal_pemohon,
                (int) $this->id_jadwal_pengganti,
                $this->alasan_tukar
            );

            session()->flash('success', 'Permohonan tukar shift berhasil diajukan dan dikirim ke rekan pengganti.');
            $this->isTukarShiftModalOpen = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function submitLembur(OvertimeService $service): void
    {
        $this->validate([
            'tgl_lembur' => 'required|date',
            'jam_mulai_lembur' => 'required',
            'jam_selesai_lembur' => 'required',
            'alasan_lembur' => 'required|min:5',
        ], [
            'alasan_lembur.required' => 'Alasan pengajuan lembur wajib diisi.',
        ]);

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $service->ajukanLembur(
                $user->karyawan,
                $this->tgl_lembur,
                $this->jam_mulai_lembur,
                $this->jam_selesai_lembur,
                $this->alasan_lembur
            );

            session()->flash('success', 'Pengajuan lembur berhasil diajukan.');
            $this->isLemburModalOpen = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function responTukarPengganti(int $idTukar, bool $setuju, TukarShiftService $service): void
    {
        try {
            $tukar = TukarShift::findOrFail($idTukar);
            $this->authorize('responPengganti', $tukar);
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $service->responPengganti($tukar, $user, $setuju);
            session()->flash('success', $setuju ? 'Anda menyetujui pertukaran shift. Menunggu konfirmasi Supervisor.' : 'Permohonan tukar shift telah ditolak.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function approveTukarSupervisor(int $idTukar, bool $setuju, TukarShiftService $service): void
    {
        try {
            $tukar = TukarShift::findOrFail($idTukar);
            $this->authorize('approveSupervisor', $tukar);
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $service->approveSupervisor($tukar, $user, $setuju);
            session()->flash('success', $setuju ? 'Tukar shift disetujui. Jadwal kedua karyawan telah otomatis diperbarui.' : 'Tukar shift ditolak.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function approveLemburSupervisor(int $idLembur, bool $setuju, OvertimeService $service): void
    {
        try {
            $lembur = Lembur::findOrFail($idLembur);
            $this->authorize('approve', $lembur);
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $service->approveLembur($lembur, $user, $setuju);
            session()->flash('success', $setuju ? 'Pengajuan lembur telah disetujui.' : 'Pengajuan lembur ditolak.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function approvePengajuan(int $idPengajuan, bool $setuju, PengajuanService $service): void
    {
        try {
            $pengajuan = Pengajuan::with('karyawan.jabatan')->findOrFail($idPengajuan);
            $this->authorize('approve', $pengajuan);
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $service->approve($pengajuan, $user, $setuju);
            session()->flash('success', $setuju ? 'Permohonan berhasil diproses.' : 'Permohonan telah ditolak.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isOperator = $user->isOperator();
        $userDept = $user->karyawan?->id_departemen;

        // Query Tukar Shift - Eager load approver.karyawan & pengganti.departemen to avoid N+1
        $tukarQuery = TukarShift::with([
            'pemohon.departemen',
            'pengganti.departemen',
            'jadwalPemohon.shift',
            'jadwalPengganti.shift',
            'approver.karyawan',
        ])
            ->when($isOperator, function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('id_pemohon', $user->id_karyawan)
                        ->orWhere('id_pengganti', $user->id_karyawan);
                });
            })
            ->when(!$isOperator && !$user->isAdminHr() && !$user->isDirektur() && $userDept, function ($q) use ($userDept) {
                $q->whereHas('pemohon', fn($sub) => $sub->where('id_departemen', $userDept));
            })
            ->when($this->filterDepartemen, function ($q) {
                $q->whereHas('pemohon', fn($sub) => $sub->where('id_departemen', $this->filterDepartemen));
            })
            ->when($this->filterStatus === 'MENUNGGU', fn($q) => $q->whereIn('status', ['MENUNGGU_PENGGANTI', 'MENUNGGU_SUPERVISOR']))
            ->when($this->filterStatus === 'DISETUJUI', fn($q) => $q->where('status', 'DISETUJUI'))
            ->when($this->filterStatus === 'DITOLAK', fn($q) => $q->where('status', 'DITOLAK'))
            ->latest();

        // Query Lembur - Eager load approver.karyawan
        $lemburQuery = Lembur::with(['karyawan.departemen', 'approver.karyawan'])
            ->when($isOperator, fn($q) => $q->where('id_karyawan', $user->id_karyawan))
            ->when(!$isOperator && !$user->isAdminHr() && !$user->isDirektur() && $userDept, function ($q) use ($userDept) {
                $q->whereHas('karyawan', fn($sub) => $sub->where('id_departemen', $userDept));
            })
            ->when($this->filterDepartemen, function ($q) {
                $q->whereHas('karyawan', fn($sub) => $sub->where('id_departemen', $this->filterDepartemen));
            })
            ->when($this->filterStatus === 'MENUNGGU', fn($q) => $q->where('status', 'DIAJUKAN'))
            ->when($this->filterStatus === 'DISETUJUI', fn($q) => $q->where('status', 'DISETUJUI'))
            ->when($this->filterStatus === 'DITOLAK', fn($q) => $q->where('status', 'DITOLAK'))
            ->latest();

        // Query Pengajuan (Cuti/Izin/Sakit) - Eager load approver.karyawan
        $pengajuanQuery = Pengajuan::with(['karyawan.departemen', 'approver.karyawan'])
            ->when($isOperator, fn($q) => $q->where('id_karyawan', $user->id_karyawan))
            ->when(!$isOperator && !$user->isAdminHr() && !$user->isDirektur() && $userDept, function ($q) use ($userDept) {
                $q->whereHas('karyawan', fn($sub) => $sub->where('id_departemen', $userDept));
            })
            ->when($this->filterDepartemen, function ($q) {
                $q->whereHas('karyawan', fn($sub) => $sub->where('id_departemen', $this->filterDepartemen));
            })
            ->when($this->filterStatus === 'MENUNGGU', fn($q) => $q->where('status', 'DIAJUKAN'))
            ->when($this->filterStatus === 'DISETUJUI', fn($q) => $q->where('status', 'DISETUJUI'))
            ->when($this->filterStatus === 'DITOLAK', fn($q) => $q->where('status', 'DITOLAK'))
            ->latest();

        // Count totals for KPI cards
        $countMenunggu = TukarShift::whereIn('status', ['MENUNGGU_PENGGANTI', 'MENUNGGU_SUPERVISOR'])->count()
            + Lembur::where('status', 'DIAJUKAN')->count()
            + Pengajuan::where('status', 'DIAJUKAN')->count();

        $countDisetujui = TukarShift::where('status', 'DISETUJUI')->count()
            + Lembur::where('status', 'DISETUJUI')->count()
            + Pengajuan::where('status', 'DISETUJUI')->count();

        $countDitolak = TukarShift::where('status', 'DITOLAK')->count()
            + Lembur::where('status', 'DITOLAK')->count()
            + Pengajuan::where('status', 'DITOLAK')->count();

        // Fetch collections based on active type filter
        $tukarList = ($this->filterTipe === 'SEMUA' || $this->filterTipe === 'TUKAR_SHIFT') ? $tukarQuery->limit(20)->get() : collect();
        $lemburList = ($this->filterTipe === 'SEMUA' || $this->filterTipe === 'LEMBUR') ? $lemburQuery->limit(20)->get() : collect();
        $pengajuanList = ($this->filterTipe === 'SEMUA' || $this->filterTipe === 'CUTI_IZIN') ? $pengajuanQuery->limit(20)->get() : collect();

        // Details of selected item
        $detailItem = null;
        if ($this->selectedType === 'TUKAR_SHIFT' && $this->selectedId) {
            $detailItem = TukarShift::with(['pemohon.departemen', 'pengganti.departemen', 'jadwalPemohon.shift', 'jadwalPengganti.shift', 'approver.karyawan'])->find($this->selectedId);
        } elseif ($this->selectedType === 'LEMBUR' && $this->selectedId) {
            $detailItem = Lembur::with(['karyawan.departemen', 'approver.karyawan'])->find($this->selectedId);
        } elseif ($this->selectedType === 'PENGAJUAN' && $this->selectedId) {
            $detailItem = Pengajuan::with(['karyawan.departemen', 'approver.karyawan'])->find($this->selectedId);
        }

        // Colleague selection for tukar shift
        $colleagues = collect();
        $mySchedules = collect();
        $partnerSchedules = collect();

        if ($this->isTukarShiftModalOpen) {
            $myDept = $user->karyawan?->id_departemen;
            $colleagues = Karyawan::where('id_departemen', $myDept)
                ->where('id_karyawan', '!=', $user->id_karyawan)
                ->where('status_karyawan', 'AKTIF')
                ->get();

            $mySchedules = JadwalShift::with('shift')
                ->where('id_karyawan', $user->id_karyawan)
                ->where('status', 'PUBLISHED')
                ->where('tanggal', '>=', now('Asia/Jakarta')->toDateString())
                ->orderBy('tanggal')
                ->limit(14)
                ->get();

            if ($this->id_pengganti) {
                $partnerSchedules = JadwalShift::with('shift')
                    ->where('id_karyawan', $this->id_pengganti)
                    ->where('status', 'PUBLISHED')
                    ->where('tanggal', '>=', now('Asia/Jakarta')->toDateString())
                    ->orderBy('tanggal')
                    ->limit(14)
                    ->get();
            }
        }

        return view('livewire.pengajuan-approval-hub', [
            'countMenunggu' => $countMenunggu,
            'countDisetujui' => $countDisetujui,
            'countDitolak' => $countDitolak,
            'tukarList' => $tukarList,
            'lemburList' => $lemburList,
            'pengajuanList' => $pengajuanList,
            'detailItem' => $detailItem,
            'colleagues' => $colleagues,
            'mySchedules' => $mySchedules,
            'partnerSchedules' => $partnerSchedules,
            'departemens' => Departemen::all(),
        ]);
    }
}
