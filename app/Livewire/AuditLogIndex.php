<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Audit Log Sistem')]
class AuditLogIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterTabel = '';
    public string $filterAksi = '';

    // Detail modal state
    public bool $isDetailModalOpen = false;
    public ?AuditLog $selectedLog = null;

    public function boot(): void
    {
        $this->authorize('viewAny', AuditLog::class);
    }

    public function mount(): void
    {
        $this->authorize('viewAny', AuditLog::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterTabel(): void
    {
        $this->resetPage();
    }

    public function updatingFilterAksi(): void
    {
        $this->resetPage();
    }

    public function showDetail(int $idLog): void
    {
        $this->selectedLog = AuditLog::with('user.karyawan')->findOrFail($idLog);
        $this->authorize('view', $this->selectedLog);
        $this->isDetailModalOpen = true;
    }

    public function render()
    {
        $query = AuditLog::with('user.karyawan')
            ->when($this->filterTabel, fn($q) => $q->where('tabel', $this->filterTabel))
            ->when($this->filterAksi, fn($q) => $q->where('aksi', $this->filterAksi))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('aksi', 'like', '%' . $this->search . '%')
                        ->orWhere('tabel', 'like', '%' . $this->search . '%')
                        ->orWhere('id_record', 'like', '%' . $this->search . '%')
                        ->orWhere('ip_address', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user.karyawan', fn($k) => $k->where('nama_karyawan', 'like', '%' . $this->search . '%'));
                });
            })
            ->latest('id_log');

        $logs = $query->paginate(20);

        $tables = AuditLog::select('tabel')->distinct()->pluck('tabel')->toArray();
        $actions = AuditLog::select('aksi')->distinct()->pluck('aksi')->toArray();

        return view('livewire.audit-log-index', [
            'logs' => $logs,
            'tables' => $tables,
            'actions' => $actions,
        ]);
    }
}
