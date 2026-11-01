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
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    /**
     * Hitung metrik dan data rekapitulasi karyawan untuk periode dan departemen yang dipilih.
     */
    protected function getReportData(bool $paginate = true): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $carbonMonth = Carbon::createFromFormat('Y-m', $this->filterBulan);
        $year = $carbonMonth->year;
        $month = $carbonMonth->month;

        // Query karyawan
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

        $karyawans = $paginate 
            ? $karyawanQuery->orderBy('id_departemen')->orderBy('id_karyawan')->paginate(15)
            : $karyawanQuery->orderBy('id_departemen')->orderBy('id_karyawan')->get();

        $karyawanIds = $karyawans->pluck('id_karyawan')->toArray();

        // Agregasi absensi
        $absensiRecords = Absensi::whereIn('id_karyawan', $karyawanIds)
            ->whereYear('tanggal_shift', $year)
            ->whereMonth('tanggal_shift', $month)
            ->get();

        // Agregasi lembur
        $lemburRecords = Lembur::whereIn('id_karyawan', $karyawanIds)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('status', 'DISETUJUI')
            ->get();

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

        // Global KPI summary
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

        $dept = $this->filterDepartemen ? Departemen::find($this->filterDepartemen) : null;
        $departemenLabel = $dept ? $dept->nama_departemen : 'Semua Departemen';

        return [
            'carbonMonth' => $carbonMonth,
            'periodeLabel' => $carbonMonth->translatedFormat('F Y'),
            'karyawans' => $karyawans,
            'statsByEmp' => $statsByEmp,
            'kpiHadir' => $kpiHadir,
            'kpiTerlambat' => $kpiTerlambat,
            'kpiAlpa' => $kpiAlpa,
            'kpiIzinCuti' => $kpiIzinCuti,
            'departemenLabel' => $departemenLabel,
            'dicetakOleh' => $user->nama . ' (' . $user->role . ')',
            'tanggalCetak' => now('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB',
        ];
    }

    /**
     * Unduh Laporan Rekapitulasi Format Excel / CSV (F-09 & FR-20).
     */
    public function exportExcel(): StreamedResponse
    {
        $data = $this->getReportData(paginate: false);
        $filename = 'rekap_absensi_' . $this->filterBulan . '.csv';

        return response()->streamDownload(function () use ($data) {
            $output = fopen('php://output', 'w');

            // UTF-8 BOM untuk Microsoft Excel agar format teks bahasa Indonesia terbaca sempurna
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Judul dan Header Informasi
            fputcsv($output, ['SIM-SHIFT DTY INDORAMA - LAPORAN REKAPITULASI ABSENSI BULANAN']);
            fputcsv($output, ['Periode', $data['periodeLabel']]);
            fputcsv($output, ['Departemen', $data['departemenLabel']]);
            fputcsv($output, ['Dicetak Oleh', $data['dicetakOleh']]);
            fputcsv($output, ['Tanggal Cetak', $data['tanggalCetak']]);
            fputcsv($output, []); // Baris kosong

            // Ringkasan KPI
            fputcsv($output, ['RINGKASAN KEHADIRAN']);
            fputcsv($output, ['Total Hadir', $data['kpiHadir']]);
            fputcsv($output, ['Insiden Terlambat', $data['kpiTerlambat']]);
            fputcsv($output, ['Alpa', $data['kpiAlpa']]);
            fputcsv($output, ['Izin/Sakit/Cuti', $data['kpiIzinCuti']]);
            fputcsv($output, []); // Baris kosong

            // Header Tabel
            fputcsv($output, [
                'No',
                'ID Karyawan',
                'Nama Karyawan',
                'Departemen',
                'Jabatan',
                'Regu Kerja',
                'Hadir (Shift)',
                'Terlambat (Kali)',
                'Total Menit Terlambat',
                'Alpa (Hari)',
                'Izin/Sakit/Cuti (Hari)',
                'Total Lembur (Jam)',
            ]);

            // Isi Data Karyawan
            foreach ($data['karyawans'] as $idx => $emp) {
                $s = $data['statsByEmp'][$emp->id_karyawan] ?? [
                    'hadir' => 0, 'terlambat' => 0, 'menit_terlambat' => 0, 'alpa' => 0, 'izin_sakit_cuti' => 0, 'total_lembur_menit' => 0
                ];

                fputcsv($output, [
                    $idx + 1,
                    $emp->id_karyawan,
                    $emp->nama_karyawan,
                    $emp->departemen?->nama_departemen ?? '-',
                    $emp->jabatan?->nama_jabatan ?? '-',
                    $emp->grup?->nama_grup ?? 'General Shift',
                    $s['hadir'],
                    $s['terlambat'],
                    $s['menit_terlambat'],
                    $s['alpa'],
                    $s['izin_sakit_cuti'],
                    round($s['total_lembur_menit'] / 60, 1),
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Unduh Laporan Rekapitulasi Format PDF (F-09 & FR-20).
     */
    public function exportPdf(): StreamedResponse
    {
        $data = $this->getReportData(paginate: false);
        $filename = 'rekap_absensi_' . $this->filterBulan . '.pdf';

        $pdf = Pdf::loadView('exports.rekap-pdf', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function render()
    {
        $data = $this->getReportData(paginate: true);

        return view('livewire.rekap-bulanan', array_merge($data, [
            'departemens' => Departemen::all(),
        ]));
    }
}
