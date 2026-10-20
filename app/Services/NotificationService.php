<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;
use App\Models\Karyawan;

class NotificationService
{
    /**
     * Kirim notifikasi ke user tertentu.
     */
    public static function kirim(int $idUser, string $judul, string $pesan, ?string $url = null): Notifikasi
    {
        return Notifikasi::create([
            'id_user' => $idUser,
            'judul' => $judul,
            'pesan' => $pesan,
            'url' => $url,
            'dibaca_at' => null,
            'created_at' => now('Asia/Jakarta'),
        ]);
    }

    /**
     * Kirim notifikasi ke karyawan berdasarkan id_karyawan.
     */
    public static function kirimKeKaryawan(string $idKaryawan, string $judul, string $pesan, ?string $url = null): ?Notifikasi
    {
        $user = User::where('id_karyawan', $idKaryawan)->first();
        if (!$user) {
            return null;
        }

        return self::kirim($user->id_user, $judul, $pesan, $url);
    }

    /**
     * Kirim notifikasi broadcast ke seluruh user ber-role tertentu di satu departemen.
     */
    public static function kirimKeDepartemen(int $idDepartemen, string $role, string $judul, string $pesan, ?string $url = null): void
    {
        $users = User::query()
            ->where('role', $role)
            ->whereHas('karyawan', function ($q) use ($idDepartemen) {
                $q->where('id_departemen', $idDepartemen);
            })
            ->get();

        foreach ($users as $u) {
            self::kirim($u->id_user, $judul, $pesan, $url);
        }
    }

    /**
     * Hitung jumlah notifikasi belum dibaca untuk user.
     */
    public static function unreadCount(int $idUser): int
    {
        return Notifikasi::where('id_user', $idUser)->whereNull('dibaca_at')->count();
    }

    /**
     * Tandai satu notifikasi sudah dibaca.
     */
    public static function tandaiDibaca(int $idNotifikasi, int $idUser): void
    {
        Notifikasi::where('id_notifikasi', $idNotifikasi)
            ->where('id_user', $idUser)
            ->update(['dibaca_at' => now('Asia/Jakarta')]);
    }

    /**
     * Tandai seluruh notifikasi user sudah dibaca.
     */
    public static function tandaiSemuaDibaca(int $idUser): void
    {
        Notifikasi::where('id_user', $idUser)
            ->whereNull('dibaca_at')
            ->update(['dibaca_at' => now('Asia/Jakarta')]);
    }
}
