<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/master/karyawan', \App\Livewire\MasterKaryawan::class)->name('master.karyawan');
    Route::get('/pola-rotasi', \App\Livewire\PolaRotasiManager::class)->name('pola.rotasi');
    Route::get('/pengajuan-approval', \App\Livewire\PengajuanApprovalHub::class)->name('pengajuan.approval');
    Route::get('/absensi/koreksi', \App\Livewire\KoreksiAbsensiManager::class)->name('absensi.koreksi');
    Route::get('/logbook', \App\Livewire\LogbookShiftManager::class)->name('logbook');
    Route::get('/notifikasi', \App\Livewire\NotifikasiIndex::class)->name('notifikasi');
    Route::get('/hari-libur', \App\Livewire\HariLiburManager::class)->name('hari.libur');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
