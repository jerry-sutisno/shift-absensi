<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Absensi;
use App\Models\Departemen;
use App\Services\AttendanceService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KoreksiAbsensiManager extends Component
{
    use WithPagination;

    public string $filterTanggal = '';
    public ?int $filterDepartemen = null;
    public string $filterStatus = '';
    public string $search = '';

    // Modal state
    public bool $isModalOpen = false;
    public ?int $selectedAbsensiId = null;
    public ?Absensi $selectedAbsensi = null;

    // Form inputs
    public string $waktu_masuk = '';
    public string $waktu_pulang = '';
    public string $status = 'HADIR';
    public int $menit_terlambat = 0;
    public bool $pulang_cepat = false;
    public string $alasan = '';

    public function mount(): void
    {
        $this->filterTanggal = now('Asia/Jakarta')->toDateString();

        $user = Auth::user();
        if ($user && $user->karyawan && !$user->isAdminHr()) {
            $this->filterDepartemen = $user->karyawan->id_departemen;
        }
    }

    public function openKoreksiModal(int $id): void
    {
        $this->selectedAbsensiId = $id;
        $this->selectedAbsensi = Absensi::with(['karyawan.departemen', 'jadwal.shift'])->findOrFail($id);

        $this->authorize('koreksi', $this->selectedAbsensi);

        $this->waktu_masuk = $this->selectedAbsensi->waktu_masuk ? $this->selectedAbsensi->waktu_masuk->format('Y-m-d\TH:i') : '';
        $this->waktu_pulang = $this->selectedAbsensi->waktu_pulang ? $this->selectedAbsensi->waktu_pulang->format('Y-m-d\TH:i') : '';
        $this->status = $this->selectedAbsensi->status;
        $this->menit_terlambat = $this->selectedAbsensi->menit_terlambat ?? 0;
        $this->pulang_cepat = (bool) $this->selectedAbsensi->pulang_cepat;
        $this->alasan = '';

        $this->isModalOpen = true;
    }

    public function simpanKoreksi(AttendanceService $service): void
    {
        if ($this->selectedAbsensi) {
            $this->authorize('koreksi', $this->selectedAbsensi);
        }

        $this->validate([
            'alasan' => 'required|min:5',
            'status' => 'required|in:HADIR,TERLAMBAT,ALPA,IZIN,SAKIT,CUTI,LIBUR',
        ], [
            'alasan.required' => 'Alasan koreksi absensi wajib diisi (sesuai aturan BR-12).',
            'alasan.min' => 'Alasan koreksi minimal 5 karakter.',
        ]);

        try {
            $dataKoreksi = [
                'waktu_masuk' => $this->waktu_masuk ? Carbon::parse($this->waktu_masuk) : null,
                'waktu_pulang' => $this->waktu_pulang ? Carbon::parse($this->waktu_pulang) : null,
                'status' => $this->status,
                'menit_terlambat' => $this->menit_terlambat,
                'pulang_cepat' => $this->pulang_cepat,
                'metode_masuk' => 'MANUAL',
                'metode_pulang' => 'MANUAL',
            ];

            $service->koreksiAbsensi($this->selectedAbsensiId, $dataKoreksi, Auth::user(), $this->alasan);

            session()->flash('success', "Absensi karyawan {$this->selectedAbsensi->karyawan->nama_karyawan} berhasil dikoreksi dan dicatat di audit log.");
            $this->isModalOpen = false;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::user();

        $absensiList = Absensi::with(['karyawan.departemen', 'jadwal.shift', 'korektor'])
            ->visibleFor($user)
            ->when($this->filterTanggal, fn($q) => $q->where('tanggal_shift', $this->filterTanggal))
            ->when($this->filterDepartemen, function ($q) {
                $q->whereHas('karyawan', fn($sub) => $sub->where('id_departemen', $this->filterDepartemen));
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, function ($q) {
                $q->whereHas('karyawan', function ($sub) {
                    $sub->where('nama_karyawan', 'like', '%' . $this->search . '%')
                        ->orWhere('id_karyawan', 'like', '%' . $this->search . '%');
                });
            })
            ->latest('tanggal_shift')
            ->paginate(15);

        return view('livewire.koreksi-absensi-manager', [
            'absensiList' => $absensiList,
            'departemens' => Departemen::all(),
        ])->layout('layouts.app', ['title' => 'Koreksi Absensi Manual']);
    }
}
