<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\JadwalShift;
use App\Models\PolaRotasi;
use App\Models\PolaRotasiDetail;
use App\Models\HariLibur;
use App\Models\AuditLog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ScheduleGeneratorService
{
    /**
     * Generate jadwal shift bulanan untuk satu departemen.
     *
     * @param int $idDepartemen
     * @param int $tahun
     * @param int $bulan
     * @param int $idPola
     * @param string|null $tanggalMulaiSiklus
     * @return array
     */
    public function generateBulanan(
        int $idDepartemen,
        int $tahun,
        int $bulan,
        int $idPola,
        ?string $tanggalMulaiSiklus = null
    ): array {
        return DB::transaction(function () use ($idDepartemen, $tahun, $bulan, $idPola, $tanggalMulaiSiklus) {
            $pola = PolaRotasi::with('detail')->findOrFail($idPola);
            $panjangSiklus = $pola->panjang_siklus_hari;

            // Map detail: [id_grup][hari_ke] => kode_shift
            $polaMatrix = [];
            foreach ($pola->detail as $d) {
                $polaMatrix[$d->id_grup][$d->hari_ke] = $d->kode_shift;
            }

            // Ambil seluruh karyawan AKTIF di departemen terkait
            $karyawanList = Karyawan::query()
                ->where('id_departemen', $idDepartemen)
                ->where('status_karyawan', 'AKTIF')
                ->get();

            $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
            $endDate = $startDate->copy()->endOfMonth();
            $period = CarbonPeriod::create($startDate, $endDate);

            // Tanggal patokan siklus (hari_ke = 1)
            $anchorDate = $tanggalMulaiSiklus 
                ? Carbon::parse($tanggalMulaiSiklus)->startOfDay() 
                : $startDate->copy();

            $countGenerated = 0;
            $countSkipped = 0;

            // Ambil daftar hari libur nasional/perusahaan di bulan ini
            $liburList = HariLibur::query()
                ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
                ->pluck('tanggal')
                ->toArray();

            foreach ($period as $date) {
                $dateStr = $date->toDateString();
                $isWeekend = $date->isWeekend(); // Sabtu atau Minggu
                $isHoliday = in_array($dateStr, $liburList);

                // Hitung offset hari dari anchorDate
                $diffDays = $anchorDate->diffInDays($date, false);
                $hariKe = (($diffDays % $panjangSiklus) + $panjangSiklus) % $panjangSiklus + 1;

                foreach ($karyawanList as $karyawan) {
                    $kodeShift = 'OFF';

                    if ($karyawan->id_grup === null) {
                        // BR-01: Non-rotasi = GS Senin-Jumat, Sabtu-Minggu OFF, Hari Libur OFF
                        if (!$isWeekend && !$isHoliday) {
                            $kodeShift = 'GS';
                        } else {
                            $kodeShift = 'OFF';
                        }
                    } else {
                        // Karyawan rotasi regu mengikuti pola
                        $kodeShift = $polaMatrix[$karyawan->id_grup][$hariKe] ?? 'OFF';
                    }

                    // BR-02 & BR-09: Satu jadwal per tanggal, status DRAFT
                    $jadwal = JadwalShift::updateOrCreate(
                        [
                            'id_karyawan' => $karyawan->id_karyawan,
                            'tanggal' => $dateStr,
                        ],
                        [
                            'kode_shift' => $kodeShift,
                            'sumber' => 'POLA',
                            'status' => 'DRAFT',
                            'id_pola' => $idPola,
                            'dibuat_oleh' => Auth::id(),
                        ]
                    );

                    if ($jadwal->wasRecentlyCreated || $jadwal->wasChanged()) {
                        $countGenerated++;
                    } else {
                        $countSkipped++;
                    }
                }
            }

            AuditLog::catat(
                'GENERATE_JADWAL',
                'jadwal_shift',
                "Dept:{$idDepartemen}-{$tahun}-{$bulan}",
                null,
                [
                    'departemen_id' => $idDepartemen,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'id_pola' => $idPola,
                    'total_karyawan' => $karyawanList->count(),
                    'generated_count' => $countGenerated,
                ]
            );

            return [
                'status' => true,
                'total_karyawan' => $karyawanList->count(),
                'generated' => $countGenerated,
                'skipped' => $countSkipped,
                'periode' => $startDate->translatedFormat('F Y'),
            ];
        });
    }

    /**
     * Hitung preview cakupan shift per hari untuk siklus pola.
     */
    public function hitungCoveragePola(int $idPola): array
    {
        $pola = PolaRotasi::with(['detail.shift', 'detail.grup'])->find($idPola);
        if (!$pola) {
            return [];
        }

        $coverage = [];
        for ($hari = 1; $hari <= $pola->panjang_siklus_hari; $hari++) {
            $shiftCounts = [
                'S1' => 0,
                'S2' => 0,
                'S3' => 0,
                'OFF' => 0,
                'GS' => 0,
            ];

            foreach ($pola->detail->where('hari_ke', $hari) as $det) {
                if (isset($shiftCounts[$det->kode_shift])) {
                    $shiftCounts[$det->kode_shift]++;
                }
            }

            // Valid jika S1, S2, dan S3 terisi minimal 1 regu
            $isComplete = ($shiftCounts['S1'] >= 1 && $shiftCounts['S2'] >= 1 && $shiftCounts['S3'] >= 1);

            $coverage[$hari] = [
                'hari_ke' => $hari,
                'counts' => $shiftCounts,
                'is_complete' => $isComplete,
            ];
        }

        return $coverage;
    }
}
