<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HariLibur;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HariLiburManager extends Component
{
    public string $filterTahun = '';

    // Modal state
    public bool $isModalOpen = false;
    public bool $isEditMode = false;
    public string $tanggal = '';
    public string $keterangan = '';
    public string $jenis = 'NASIONAL';

    public function mount(): void
    {
        $this->filterTahun = (string) now('Asia/Jakarta')->year;
    }

    public function openCreateModal(): void
    {
        $this->reset(['tanggal', 'keterangan']);
        $this->jenis = 'NASIONAL';
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function openEditModal(string $tanggal): void
    {
        $libur = HariLibur::findOrFail($tanggal);
        $this->tanggal = $libur->tanggal->format('Y-m-d');
        $this->keterangan = $libur->keterangan;
        $this->jenis = $libur->jenis;
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function simpanHariLibur(): void
    {
        $this->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|max:100',
            'jenis' => 'required|in:NASIONAL,PERUSAHAAN',
        ], [
            'keterangan.required' => 'Nama/keterangan hari libur wajib diisi.',
        ]);

        if ($this->isEditMode) {
            $libur = HariLibur::findOrFail($this->tanggal);
            $dataLama = $libur->toArray();

            $libur->update([
                'keterangan' => $this->keterangan,
                'jenis' => $this->jenis,
            ]);

            AuditLog::catat(
                'UPDATE_HARI_LIBUR',
                'hari_libur',
                $this->tanggal,
                $dataLama,
                $libur->fresh()->toArray()
            );

            session()->flash('success', "Hari libur {$this->tanggal} berhasil diperbarui.");
        } else {
            if (HariLibur::where('tanggal', $this->tanggal)->exists()) {
                $this->addError('tanggal', 'Tanggal ini sudah terdaftar sebagai hari libur.');
                return;
            }

            $libur = HariLibur::create([
                'tanggal' => $this->tanggal,
                'keterangan' => $this->keterangan,
                'jenis' => $this->jenis,
            ]);

            AuditLog::catat(
                'CREATE_HARI_LIBUR',
                'hari_libur',
                $this->tanggal,
                null,
                $libur->toArray()
            );

            session()->flash('success', "Hari libur baru {$this->keterangan} ({$this->tanggal}) berhasil ditambahkan.");
        }

        $this->isModalOpen = false;
    }

    public function hapusHariLibur(string $tanggal): void
    {
        $libur = HariLibur::findOrFail($tanggal);
        $dataLama = $libur->toArray();
        $libur->delete();

        AuditLog::catat(
            'DELETE_HARI_LIBUR',
            'hari_libur',
            $tanggal,
            $dataLama,
            null
        );

        session()->flash('success', "Hari libur {$tanggal} berhasil dihapus.");
    }

    public function render()
    {
        $hariLiburs = HariLibur::query()
            ->when($this->filterTahun, fn($q) => $q->whereYear('tanggal', $this->filterTahun))
            ->orderBy('tanggal')
            ->get();

        return view('livewire.hari-libur-manager', [
            'hariLiburs' => $hariLiburs,
        ])->layout('layouts.app', ['title' => 'Kelola Hari Libur']);
    }
}
