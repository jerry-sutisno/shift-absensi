<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Public Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Kiosk Mode Terminal Presensi Mandiri (F-05 & FR-12)
Route::get('/kiosk', \App\Livewire\KioskPresensi::class)->name('kiosk.absensi');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/jadwal', \App\Livewire\KalenderJadwal::class)->name('jadwal.kalender');
    Route::get('/master/karyawan', \App\Livewire\MasterKaryawan::class)->name('master.karyawan');
    Route::get('/pola-rotasi', \App\Livewire\PolaRotasiManager::class)->name('pola.rotasi');
    Route::get('/pengajuan-approval', \App\Livewire\PengajuanApprovalHub::class)->name('pengajuan.approval');
    Route::get('/absensi/koreksi', \App\Livewire\KoreksiAbsensiManager::class)->name('absensi.koreksi');
    Route::get('/logbook', \App\Livewire\LogbookShiftManager::class)->name('logbook');
    Route::get('/rekap', \App\Livewire\RekapBulanan::class)->name('laporan.rekap');
    Route::get('/notifikasi', \App\Livewire\NotifikasiIndex::class)->name('notifikasi');
    Route::get('/hari-libur', \App\Livewire\HariLiburManager::class)->name('hari.libur');
    Route::get('/pengaturan', \App\Livewire\PengaturanManager::class)->name('pengaturan.sistem');
    Route::get('/audit-log', \App\Livewire\AuditLogIndex::class)->name('audit.log');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


