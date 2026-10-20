<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengajuan extends Model
{
    protected $table = 'pengajuan';
    protected $primaryKey = 'id_pengajuan';

    protected $fillable = [
        'id_karyawan',
        'jenis',
        'tgl_mulai',
        'tgl_selesai',
        'alasan',
        'lampiran_path',
        'status',
        'tahap',
        'id_approver',
        'catatan_approver',
        'keputusan_at',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_selesai' => 'date',
            'keputusan_at' => 'datetime',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_approver', 'id_user');
    }
}
