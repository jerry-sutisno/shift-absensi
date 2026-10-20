<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LogbookShift;
use App\Models\Departemen;
use App\Models\GrupKaryawan;
use App\Models\Shift;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LogbookShiftManager extends Component
{
    use WithPagination;

    public string $filterTanggal = '';
    public ?int $filterDepartemen = null;
    public string $filterShift = '';

    // Create Modal state
    public bool $isModalOpen = false;
    public string $tanggal = '';
    public string $kode_shift = 'S1';
    public ?int $id_departemen = null;
    public ?int $id_grup = null;
    public string $ringkasan = '';
    public string $kendala = '';
    public string $tindak_lanjut = '';

    public function mount(): void
    {
        $this->filterTanggal = now('Asia/Jakarta')->toDateString();
        $this->tanggal = now('Asia/Jakarta')->toDateString();

        $user = Auth::user();
        if ($user && $user->karyawan) {
            $this->filterDepartemen = $user->karyawan->id_departemen;
            $this->id_departemen = $user->karyawan->id_departemen;
        } else {
            $dept = Departemen::first();
            $this->id_departemen = $dept?->id_departemen;
        }
    }

    public function openCreateModal(): void
    {
        $this->reset(['ringkasan', 'kendala', 'tindak_lanjut']);
        $this->tanggal = now('Asia/Jakarta')->toDateString();
        $this->isModalOpen = true;
    }

    public function simpanLogbook(): void
    {
        $this->validate([
            'tanggal' => 'required|date',
            'kode_shift' => 'required|exists:shift,kode_shift',
            'id_departemen' => 'required|exists:departemen,id_departemen',
            'ringkasan' => 'required|min:10',
            'tindak_lanjut' => 'nullable|string',
        ], [
            'ringkasan.required' => 'Ringkasan pekerjaan shift wajib diisi minimal 10 karakter.',
        ]);

        $logbook = LogbookShift::create([
            'tanggal' => $this->tanggal,
            'kode_shift' => $this->kode_shift,
            'id_departemen' => $this->id_departemen,
            'id_grup' => $this->id_grup ?: null,
            'id_pembuat' => Auth::id(),
            'ringkasan' => $this->ringkasan,
            'kendala' => $this->kendala ?: null,
            'tindak_lanjut' => $this->tindak_lanjut ?: null,
        ]);

        AuditLog::catat(
            'CREATE_LOGBOOK',
            'logbook_shift',
            (string) $logbook->id_logbook,
            null,
            $logbook->toArray()
        );

        session()->flash('success', "Logbook serah terima Shift {$this->kode_shift} berhasil dicatat.");
        $this->isModalOpen = false;
    }

    public function render()
    {
        $logbooks = LogbookShift::with(['shift', 'departemen', 'grup', 'pembuat'])
            ->when($this->filterTanggal, fn($q) => $q->where('tanggal', $this->filterTanggal))
            ->when($this->filterDepartemen, fn($q) => $q->where('id_departemen', $this->filterDepartemen))
            ->when($this->filterShift, fn($q) => $q->where('kode_shift', $this->filterShift))
            ->latest('tanggal')
            ->latest('id_logbook')
            ->paginate(10);

        return view('livewire.logbook-shift-manager', [
            'logbooks' => $logbooks,
            'departemens' => Departemen::all(),
            'grups' => GrupKaryawan::all(),
            'shifts' => Shift::where('is_libur', false)->get(),
        ])->layout('layouts.app', ['title' => 'Logbook Serah Terima Shift']);
    }
}
