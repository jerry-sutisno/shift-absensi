<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TukarShift extends Model
{
    protected $table = 'tukar_shift';
    protected $primaryKey = 'id_tukar';

    protected $fillable = [
        'id_pemohon',
        'id_pengganti',
        'id_jadwal_pemohon',
        'id_jadwal_pengganti',
        'alasan',
        'status',
        'id_approver',
        'keputusan_at',
    ];

    protected function casts(): array
    {
        return [
            'keputusan_at' => 'datetime',
        ];
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_pemohon', 'id_karyawan');
    }

    public function pengganti(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_pengganti', 'id_karyawan');
    }

    public function jadwalPemohon(): BelongsTo
    {
        return $this->belongsTo(JadwalShift::class, 'id_jadwal_pemohon', 'id_jadwal');
    }

    public function jadwalPengganti(): BelongsTo
    {
        return $this->belongsTo(JadwalShift::class, 'id_jadwal_pengganti', 'id_jadwal');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_approver', 'id_user');
    }
}
