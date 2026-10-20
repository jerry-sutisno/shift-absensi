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

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
