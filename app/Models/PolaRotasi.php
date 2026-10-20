<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PolaRotasi extends Model
{
    protected $table = 'pola_rotasi';
    protected $primaryKey = 'id_pola';

    protected $fillable = [
        'nama_pola',
        'panjang_siklus_hari',
        'keterangan',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'panjang_siklus_hari' => 'integer',
            'is_aktif' => 'boolean',
        ];
    }

    public function detail(): HasMany
    {
        return $this->hasMany(PolaRotasiDetail::class, 'id_pola', 'id_pola');
    }

    public function jadwalShift(): HasMany
    {
        return $this->hasMany(JadwalShift::class, 'id_pola', 'id_pola');
    }
}
