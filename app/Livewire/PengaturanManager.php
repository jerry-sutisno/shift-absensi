<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Pengaturan;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('Pengaturan Sistem')]
class PengaturanManager extends Component
{
    public array $settings = [];

    // Modal state for editing a setting
    public bool $isModalOpen = false;
    public string $selectedKey = '';
    public string $selectedLabel = '';
    public string $selectedValue = '';
    public string $selectedDescription = '';

    public function boot(): void
    {
        $this->authorize('viewAny', Pengaturan::class);
    }

    public function mount(): void
    {
        $this->authorize('viewAny', Pengaturan::class);
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $all = Pengaturan::all();
        $this->settings = $all->keyBy('kunci')->toArray();
    }

    public function editSetting(string $kunci): void
    {
        $setting = Pengaturan::findOrFail($kunci);
        $this->authorize('update', $setting);

        $this->selectedKey = $setting->kunci;
        $this->selectedValue = $setting->nilai;
        $this->selectedDescription = $setting->keterangan ?? '';
        $this->selectedLabel = ucwords(str_replace('_', ' ', $setting->kunci));
        $this->isModalOpen = true;
    }

    public function simpanSetting(): void
    {
        $setting = Pengaturan::findOrFail($this->selectedKey);
        $this->authorize('update', $setting);

        $this->validate([
            'selectedValue' => 'required|numeric|min:0',
        ], [
            'selectedValue.required' => 'Nilai parameter wajib diisi.',
            'selectedValue.numeric' => 'Nilai parameter harus berupa angka.',
        ]);
        $oldValue = $setting->nilai;

        $setting->update([
            'nilai' => (string) $this->selectedValue,
        ]);

        AuditLog::catat(
            'UPDATE_PENGATURAN',
            'pengaturan',
            $this->selectedKey,
            ['nilai' => $oldValue],
            ['nilai' => (string) $this->selectedValue]
        );

        $this->isModalOpen = false;
        $this->loadSettings();

        session()->flash('success', "Parameter {$this->selectedKey} berhasil diperbarui menjadi {$this->selectedValue}.");
    }

    public function render()
    {
        return view('livewire.pengaturan-manager', [
            'allSettings' => Pengaturan::orderBy('kunci')->get(),
        ]);
    }
}
