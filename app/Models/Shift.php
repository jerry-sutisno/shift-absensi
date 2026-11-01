<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $kode_shift
 * @property string $nama_shift
 * @property string|null $jam_masuk
 * @property string|null $jam_pulang
 * @property bool $lintas_hari
 * @property int $toleransi_menit
 * @property string|null $warna
 * @property bool $is_libur
 * @property bool $is_aktif
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JadwalShift> $jadwalShift
 * @property-read int|null $jadwal_shift_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PolaRotasiDetail> $polaRotasiDetail
 * @property-read int|null $pola_rotasi_detail_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereIsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereIsLibur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereJamMasuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereJamPulang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereKodeShift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereLintasHari($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereNamaShift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereToleransiMenit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereWarna($value)
 * @mixin \Eloquent
 */
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
