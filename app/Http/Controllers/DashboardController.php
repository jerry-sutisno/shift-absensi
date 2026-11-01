<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Departemen;
use App\Models\GrupKaryawan;
use App\Models\Shift;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalKaryawan = Karyawan::where('status_karyawan', 'AKTIF')->count();
        $totalDepartemen = Departemen::count();
        $totalGrup = GrupKaryawan::count();
        $totalShift = Shift::count();
        $shifts = Shift::all();

        return view('dashboard', compact(
            'totalKaryawan',
            'totalDepartemen',
            'totalGrup',
            'totalShift',
            'shifts'
        ));
    }
}
