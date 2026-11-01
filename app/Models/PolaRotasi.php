<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_pola
 * @property string $nama_pola
 * @property int $panjang_siklus_hari
 * @property string|null $keterangan
 * @property bool $is_aktif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PolaRotasiDetail> $detail
 * @property-read int|null $detail_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JadwalShift> $jadwalShift
 * @property-read int|null $jadwal_shift_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasi whereIdPola($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasi whereIsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasi whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasi whereNamaPola($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasi wherePanjangSiklusHari($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasi whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
