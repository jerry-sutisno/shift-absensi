<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Http\Requests\LoginRequest;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        $throttleKey = Str::transliterate(Str::lower($request->input('id_karyawan')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'id_karyawan' => "Terlalu banyak percobaan masuk. Silakan coba lagi dalam {$seconds} detik.",
            ])->onlyInput('id_karyawan');
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt([
            'id_karyawan' => $credentials['id_karyawan'],
            'password' => $credentials['password'],
            'is_active' => true,
        ], $remember)) {
            RateLimiter::clear($throttleKey);

            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->update(['last_login_at' => now('Asia/Jakarta')]);

            $request->session()->regenerate();

            AuditLog::catat(
                'LOGIN',
                'users',
                (string) $user->id_user,
                null,
                ['id_karyawan' => $user->id_karyawan, 'role' => $user->role],
                $user->id_user
            );

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'id_karyawan' => 'ID Karyawan atau password tidak sesuai, atau akun Anda nonaktif.',
        ])->onlyInput('id_karyawan');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            AuditLog::catat(
                'LOGOUT',
                'users',
                (string) $user->id_user,
                null,
                ['id_karyawan' => $user->id_karyawan],
                $user->id_user
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
