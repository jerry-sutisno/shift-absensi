<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Notifikasi;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotifikasiIndex extends Component
{
    use WithPagination;

    public string $filter = 'SEMUA'; // SEMUA, BELUM_DIBACA

    public function tandaiDibaca(int $id): void
    {
        NotificationService::tandaiDibaca($id, Auth::id());
        session()->flash('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function tandaiSemuaDibaca(): void
    {
        NotificationService::tandaiSemuaDibaca(Auth::id());
        session()->flash('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function render()
    {
        $notifications = Notifikasi::where('id_user', Auth::id())
            ->when($this->filter === 'BELUM_DIBACA', fn($q) => $q->whereNull('dibaca_at'))
            ->latest('created_at')
            ->paginate(15);

        $unreadCount = NotificationService::unreadCount(Auth::id());

        return view('livewire.notifikasi-index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ])->layout('layouts.app', ['title' => 'Pusat Notifikasi']);
    }
}
