<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lembur extends Model
{
    protected $table = 'lembur';
    protected $primaryKey = 'id_lembur';

    protected $fillable = [
        'id_karyawan',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'alasan',
        'status',
        'id_approver',
        'menit_lembur',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'menit_lembur' => 'integer',
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
