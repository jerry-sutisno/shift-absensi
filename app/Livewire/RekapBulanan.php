<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\Lembur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Rekapitulasi Absensi Bulanan')]
class RekapBulanan extends Component
{
    use WithPagination;

    public string $filterBulan = '';
    public ?int $filterDepartemen = null;
    public string $search = '';

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Operator does not have access to monthly recap
        if ($user->isOperator()) {
            abort(403, 'Operator tidak memiliki izin mengakses rekap bulanan.');
        }

        $this->filterBulan = now('Asia/Jakarta')->format('Y-m');

        if ($user->isHod() || $user->isSupervisor()) {
            $this->filterDepartemen = $user->karyawan?->id_departemen;
        } else {
            $this->filterDepartemen = null;
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterBulan(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDepartemen(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $carbonMonth = Carbon::createFromFormat('Y-m', $this->filterBulan);
        $year = $carbonMonth->year;
        $month = $carbonMonth->month;

        // Employees query
        $karyawanQuery = Karyawan::with(['departemen', 'grup', 'jabatan'])
            ->where('status_karyawan', 'AKTIF');

        if ($user->isHod() || $user->isSupervisor()) {
            $karyawanQuery->where('id_departemen', $user->karyawan?->id_departemen);
        } elseif ($this->filterDepartemen) {
            $karyawanQuery->where('id_departemen', $this->filterDepartemen);
        }

        if ($this->search) {
            $karyawanQuery->where(function ($q) {
                $q->where('nama_karyawan', 'like', '%' . $this->search . '%')
                  ->orWhere('id_karyawan', 'like', '%' . $this->search . '%');
            });
        }

        $karyawans = $karyawanQuery->orderBy('id_departemen')->orderBy('id_karyawan')->paginate(15);
        $karyawanIds = $karyawans->pluck('id_karyawan')->toArray();

        // Absensi aggregations for these employees in this month
        $absensiRecords = Absensi::whereIn('id_karyawan', $karyawanIds)
            ->whereYear('tanggal_shift', $year)
            ->whereMonth('tanggal_shift', $month)
            ->get();

        // Lembur aggregations for these employees in this month
        $lemburRecords = Lembur::whereIn('id_karyawan', $karyawanIds)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('status', 'DISETUJUI')
            ->get();

        // Calculate per-employee stats
        $statsByEmp = [];
        foreach ($karyawanIds as $id) {
            $statsByEmp[$id] = [
                'hadir' => 0,
                'terlambat' => 0,
                'menit_terlambat' => 0,
                'alpa' => 0,
                'izin_sakit_cuti' => 0,
                'total_lembur_menit' => 0,
            ];
        }

        foreach ($absensiRecords as $a) {
            $id = $a->id_karyawan;
            if (!isset($statsByEmp[$id])) continue;

            if ($a->status === 'HADIR') {
                $statsByEmp[$id]['hadir']++;
            } elseif ($a->status === 'TERLAMBAT') {
                $statsByEmp[$id]['hadir']++;
                $statsByEmp[$id]['terlambat']++;
                $statsByEmp[$id]['menit_terlambat'] += $a->menit_terlambat ?? 0;
            } elseif ($a->status === 'ALPA') {
                $statsByEmp[$id]['alpa']++;
            } elseif (in_array($a->status, ['IZIN', 'SAKIT', 'CUTI'])) {
                $statsByEmp[$id]['izin_sakit_cuti']++;
            }
        }

        foreach ($lemburRecords as $l) {
            $id = $l->id_karyawan;
            if (!isset($statsByEmp[$id])) continue;
            $statsByEmp[$id]['total_lembur_menit'] += $l->menit_lembur ?? 0;
        }

        // Global KPI summary for current filter
        $allDeptAbsensi = Absensi::whereYear('tanggal_shift', $year)
            ->whereMonth('tanggal_shift', $month);

        if ($user->isHod() || $user->isSupervisor()) {
            $allDeptAbsensi->whereHas('karyawan', fn($q) => $q->where('id_departemen', $user->karyawan?->id_departemen));
        } elseif ($this->filterDepartemen) {
            $allDeptAbsensi->whereHas('karyawan', fn($q) => $q->where('id_departemen', $this->filterDepartemen));
        }

        $kpiHadir = (clone $allDeptAbsensi)->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
        $kpiTerlambat = (clone $allDeptAbsensi)->where('status', 'TERLAMBAT')->count();
        $kpiAlpa = (clone $allDeptAbsensi)->where('status', 'ALPA')->count();
        $kpiIzinCuti = (clone $allDeptAbsensi)->whereIn('status', ['IZIN', 'SAKIT', 'CUTI'])->count();

        return view('livewire.rekap-bulanan', [
            'carbonMonth' => $carbonMonth,
            'karyawans' => $karyawans,
            'statsByEmp' => $statsByEmp,
            'kpiHadir' => $kpiHadir,
            'kpiTerlambat' => $kpiTerlambat,
            'kpiAlpa' => $kpiAlpa,
            'kpiIzinCuti' => $kpiIzinCuti,
            'departemens' => Departemen::all(),
        ]);
    }
}
