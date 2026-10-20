<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $table = 'shift';
    protected $primaryKey = 'kode_shift';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_shift',
        'nama_shift',
        'jam_masuk',
        'jam_pulang',
        'lintas_hari',
        'toleransi_menit',
        'warna',
        'is_libur',
        'is_aktif',
    ];

    protected function casts(): array
    {
        return [
            'lintas_hari' => 'boolean',
            'is_libur' => 'boolean',
            'is_aktif' => 'boolean',
            'toleransi_menit' => 'integer',
        ];
    }

    public function jadwalShift(): HasMany
    {
        return $this->hasMany(JadwalShift::class, 'kode_shift', 'kode_shift');
    }

    public function polaRotasiDetail(): HasMany
    {
        return $this->hasMany(PolaRotasiDetail::class, 'kode_shift', 'kode_shift');
    }
}
