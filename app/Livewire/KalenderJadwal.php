<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\JadwalShift;
use App\Models\Departemen;
use App\Models\GrupKaryawan;
use App\Models\Shift;
use App\Models\Karyawan;
use App\Models\AuditLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Kalender Jadwal Shift')]
class KalenderJadwal extends Component
{
    public string $filterBulan = '';
    public ?int $filterDepartemen = null;
    public ?int $filterGrup = null;
    public string $filterShift = '';
    public string $search = '';

    public function mount(): void
    {
        $this->filterBulan = now('Asia/Jakarta')->format('Y-m');

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user && $user->karyawan && !$user->isAdminHr() && !$user->isDirektur()) {
            $this->filterDepartemen = $user->karyawan->id_departemen;
        } else {
            $this->filterDepartemen = null;
        }
    }

    public function prevMonth(): void
    {
        $current = Carbon::createFromFormat('Y-m', $this->filterBulan);
        $this->filterBulan = $current->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $current = Carbon::createFromFormat('Y-m', $this->filterBulan);
        $this->filterBulan = $current->addMonth()->format('Y-m');
    }

    public function publishBulanIni(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdminHr() && !$user->isSupervisor()) {
            abort(403, 'Hanya Admin HR dan Supervisor yang berhak mempublikasikan jadwal.');
        }

        $date = Carbon::createFromFormat('Y-m', $this->filterBulan);
        $year = $date->year;
        $month = $date->month;

        $targetDept = $this->filterDepartemen;
        if ($user->isSupervisor()) {
            $targetDept = $user->karyawan?->id_departemen;
        }

        $query = JadwalShift::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('status', 'DRAFT');

        if ($targetDept) {
            $query->whereHas('karyawan', fn($q) => $q->where('id_departemen', $targetDept));
        }

        $count = $query->count();
        $query->update(['status' => 'PUBLISHED']);

        AuditLog::catat(
            'PUBLISH_JADWAL',
            'jadwal_shift',
            "{$year}-{$month}",
            ['status' => 'DRAFT'],
            ['status' => 'PUBLISHED', 'count' => $count]
        );

        session()->flash('success', "{$count} jadwal shift periode {$date->translatedFormat('F Y')} berhasil dipublikasikan ke operator.");
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isOperator = $user->isOperator();

        $carbonMonth = Carbon::createFromFormat('Y-m', $this->filterBulan);
        $startDate = $carbonMonth->copy()->startOfMonth();
        $endDate = $carbonMonth->copy()->endOfMonth();
        $daysInMonth = CarbonPeriod::create($startDate, $endDate);

        // Karyawan query
        $karyawanQuery = Karyawan::with(['departemen', 'grup'])
            ->where('status_karyawan', 'AKTIF');

        if ($isOperator) {
            $karyawanQuery->where('id_karyawan', $user->id_karyawan);
        } else {
            if ($user->isHod() || $user->isSupervisor()) {
                $karyawanQuery->where('id_departemen', $user->karyawan?->id_departemen);
            } elseif ($this->filterDepartemen) {
                $karyawanQuery->where('id_departemen', $this->filterDepartemen);
            }

            if ($this->filterGrup) {
                $karyawanQuery->where('id_grup', $this->filterGrup);
            }

            if ($this->search) {
                $karyawanQuery->where(function ($q) {
                    $q->where('nama_karyawan', 'like', '%' . $this->search . '%')
                      ->orWhere('id_karyawan', 'like', '%' . $this->search . '%');
                });
            }
        }

        $karyawanList = $karyawanQuery->orderBy('id_departemen')->orderBy('id_grup')->orderBy('nama_karyawan')->get();
        $karyawanIds = $karyawanList->pluck('id_karyawan')->toArray();

        // Jadwal Shift query
        $jadwalQuery = JadwalShift::with('shift')
            ->whereIn('id_karyawan', $karyawanIds)
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($isOperator) {
            $jadwalQuery->where('status', 'PUBLISHED');
        }

        if ($this->filterShift) {
            $jadwalQuery->where('kode_shift', $this->filterShift);
        }

        $schedules = $jadwalQuery->get();

        // Index schedules by "id_karyawan_YYYY-MM-DD"
        $scheduleMatrix = [];
        $totalPublished = 0;
        $totalDraft = 0;
        $totalOff = 0;

        foreach ($schedules as $j) {
            $key = $j->id_karyawan . '_' . $j->tanggal->toDateString();
            $scheduleMatrix[$key] = $j;

            if ($j->kode_shift === 'OFF') {
                $totalOff++;
            } elseif ($j->status === 'PUBLISHED') {
                $totalPublished++;
            } elseif ($j->status === 'DRAFT') {
                $totalDraft++;
            }
        }

        return view('livewire.kalender-jadwal', [
            'carbonMonth' => $carbonMonth,
            'daysInMonth' => $daysInMonth,
            'karyawanList' => $karyawanList,
            'scheduleMatrix' => $scheduleMatrix,
            'totalPublished' => $totalPublished,
            'totalDraft' => $totalDraft,
            'totalOff' => $totalOff,
            'departemens' => Departemen::all(),
            'grups' => GrupKaryawan::all(),
            'shifts' => Shift::all(),
        ]);
    }
}
