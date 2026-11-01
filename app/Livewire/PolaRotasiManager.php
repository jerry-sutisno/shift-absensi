<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\PolaRotasi;
use App\Models\PolaRotasiDetail;
use App\Models\GrupKaryawan;
use App\Models\Shift;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\AuditLog;
use App\Services\ScheduleGeneratorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
#[Title('Pola Rotasi & Generator Jadwal')]
class PolaRotasiManager extends Component
{
    public int $selectedPolaId = 1;
    public array $matrix = [];
    public ?int $targetDepartemenId = null;
    public string $targetBulan = '';
    public string $tanggalMulaiSiklus = '';
    public bool $sesuaikanHariLibur = true;
    public bool $kunciNonRotasi = true;
    public ?array $generationResult = null;

    public function mount(): void
    {
        $this->authorize('viewAny', PolaRotasi::class);

        $pola = PolaRotasi::first();
        if ($pola) {
            $this->selectedPolaId = $pola->id_pola;
            $this->loadMatrix();
        }

        // Set default target month to current month Asia/Jakarta
        $now = now('Asia/Jakarta');
        $this->targetBulan = $now->format('Y-m');
        $this->tanggalMulaiSiklus = $now->startOfMonth()->format('Y-m-d');

        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if ($user && ($user->isHod() || $user->isSupervisor())) {
            $this->targetDepartemenId = $user->karyawan?->id_departemen;
        } else {
            // Default to departemen Produksi if exists, or first departemen
            $dept = Departemen::where('kode_departemen', 'PRD')->first() ?? Departemen::first();
            if ($dept) {
                $this->targetDepartemenId = $dept->id_departemen;
            }
        }
    }

    public function updatedSelectedPolaId(): void
    {
        $this->loadMatrix();
    }

    public function updatedTargetBulan($val): void
    {
        if ($val) {
            $this->tanggalMulaiSiklus = Carbon::parse($val . '-01')->format('Y-m-d');
        }
    }

    public function loadMatrix(): void
    {
        $details = PolaRotasiDetail::where('id_pola', $this->selectedPolaId)->get();
        $this->matrix = [];

        foreach ($details as $d) {
            $this->matrix[$d->id_grup][$d->hari_ke] = $d->kode_shift;
        }
    }

    public function cycleShiftCell(int $idGrup, int $hariKe): void
    {
        $pola = PolaRotasi::findOrFail($this->selectedPolaId);
        $this->authorize('update', $pola);

        $cycle = ['S1' => 'S2', 'S2' => 'S3', 'S3' => 'OFF', 'OFF' => 'GS', 'GS' => 'S1'];
        $current = $this->matrix[$idGrup][$hariKe] ?? 'OFF';
        $next = $cycle[$current] ?? 'S1';

        $this->matrix[$idGrup][$hariKe] = $next;

        PolaRotasiDetail::updateOrCreate(
            [
                'id_pola' => $this->selectedPolaId,
                'id_grup' => $idGrup,
                'hari_ke' => $hariKe,
            ],
            [
                'kode_shift' => $next,
            ]
        );
    }

    public function updateShiftCell(int $idGrup, int $hariKe, string $kodeShift): void
    {
        $pola = PolaRotasi::findOrFail($this->selectedPolaId);
        $this->authorize('update', $pola);

        $this->matrix[$idGrup][$hariKe] = $kodeShift;

        PolaRotasiDetail::updateOrCreate(
            [
                'id_pola' => $this->selectedPolaId,
                'id_grup' => $idGrup,
                'hari_ke' => $hariKe,
            ],
            [
                'kode_shift' => $kodeShift,
            ]
        );
    }

    public function generateJadwal(ScheduleGeneratorService $service): void
    {
        $this->validate([
            'targetDepartemenId' => 'required|exists:departemen,id_departemen',
            'targetBulan' => 'required',
            'tanggalMulaiSiklus' => 'required|date',
            'selectedPolaId' => 'required|exists:pola_rotasi,id_pola',
        ]);

        $this->authorize('generate', [PolaRotasi::class, (int) $this->targetDepartemenId]);

        [$tahun, $bulan] = explode('-', $this->targetBulan);

        $result = $service->generateBulanan(
            (int) $this->targetDepartemenId,
            (int) $tahun,
            (int) $bulan,
            (int) $this->selectedPolaId,
            $this->tanggalMulaiSiklus
        );

        $this->generationResult = $result;
        session()->flash('success', "Jadwal draf berhasil di-generate untuk {$result['total_karyawan']} karyawan ({$result['generated']} shift dibuat).");
    }

    public function render()
    {
        $polas = PolaRotasi::all();
        $pola = PolaRotasi::find($this->selectedPolaId);
        $grups = GrupKaryawan::withCount(['karyawan' => function ($q) {
            $q->where('status_karyawan', 'AKTIF');
        }])->get();
        $shifts = Shift::all()->keyBy('kode_shift');
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        $departemens = Departemen::withCount(['karyawan' => function ($q) {
            $q->where('status_karyawan', 'AKTIF');
        }])
        ->when($user && ($user->isHod() || $user->isSupervisor()), fn($q) => $q->where('id_departemen', $user->karyawan?->id_departemen))
        ->get();

        // Calculate coverage matrix per hari (1 to 8)
        $coverage = [];
        $panjangSiklus = $pola ? $pola->panjang_siklus_hari : 8;

        for ($h = 1; $h <= $panjangSiklus; $h++) {
            $s1Count = 0;
            $s2Count = 0;
            $s3Count = 0;
            $offCount = 0;

            $s1Op = 0;
            $s2Op = 0;
            $s3Op = 0;

            foreach ($grups as $g) {
                $shiftCode = $this->matrix[$g->id_grup][$h] ?? 'OFF';
                $opCount = $g->karyawan_count;

                if ($shiftCode === 'S1') {
                    $s1Count++;
                    $s1Op += $opCount;
                } elseif ($shiftCode === 'S2') {
                    $s2Count++;
                    $s2Op += $opCount;
                } elseif ($shiftCode === 'S3') {
                    $s3Count++;
                    $s3Op += $opCount;
                } else {
                    $offCount++;
                }
            }

            $isComplete = ($s1Count >= 1 && $s2Count >= 1 && $s3Count >= 1);

            $coverage[$h] = [
                's1_count' => $s1Count,
                's2_count' => $s2Count,
                's3_count' => $s3Count,
                's1_op' => $s1Op,
                's2_op' => $s2Op,
                's3_op' => $s3Op,
                'is_complete' => $isComplete,
                'percentage' => $isComplete ? 100 : round((($s1Count > 0 ? 1 : 0) + ($s2Count > 0 ? 1 : 0) + ($s3Count > 0 ? 1 : 0)) / 3 * 100),
            ];
        }

        // Selected departemen stats
        $selectedDept = Departemen::withCount([
            'karyawan' => fn($q) => $q->where('status_karyawan', 'AKTIF'),
            'karyawan as operator_count' => fn($q) => $q->where('status_karyawan', 'AKTIF')->whereNotNull('id_grup'),
            'karyawan as gs_count' => fn($q) => $q->where('status_karyawan', 'AKTIF')->whereNull('id_grup'),
        ])->find($this->targetDepartemenId);

        return view('livewire.pola-rotasi-manager', [
            'polas' => $polas,
            'pola' => $pola,
            'grups' => $grups,
            'shifts' => $shifts,
            'departemens' => $departemens,
            'coverage' => $coverage,
            'panjangSiklus' => $panjangSiklus,
            'selectedDept' => $selectedDept,
        ]);
    }
}
