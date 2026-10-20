<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';

    protected $fillable = [
        'id_jadwal',
        'id_karyawan',
        'tanggal_shift',
        'waktu_masuk',
        'waktu_pulang',
        'metode_masuk',
        'metode_pulang',
        'status',
        'menit_terlambat',
        'pulang_cepat',
        'menit_kerja',
        'catatan',
        'dikoreksi_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_shift' => 'date',
            'waktu_masuk' => 'datetime',
            'waktu_pulang' => 'datetime',
            'menit_terlambat' => 'integer',
            'pulang_cepat' => 'boolean',
            'menit_kerja' => 'integer',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalShift::class, 'id_jadwal', 'id_jadwal');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function dikoreksiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikoreksi_oleh', 'id_user');
    }
}
