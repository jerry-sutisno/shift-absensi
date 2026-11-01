<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_grup
 * @property string $nama_grup
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Karyawan> $karyawan
 * @property-read int|null $karyawan_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PolaRotasiDetail> $polaRotasiDetail
 * @property-read int|null $pola_rotasi_detail_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupKaryawan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupKaryawan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupKaryawan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupKaryawan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupKaryawan whereIdGrup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupKaryawan whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupKaryawan whereNamaGrup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GrupKaryawan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GrupKaryawan extends Model
{
    protected $table = 'grup_karyawan';
    protected $primaryKey = 'id_grup';

    protected $fillable = [
        'nama_grup',
        'keterangan',
    ];

    public function karyawan(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'id_grup', 'id_grup');
    }

    public function polaRotasiDetail(): HasMany
    {
        return $this->hasMany(PolaRotasiDetail::class, 'id_grup', 'id_grup');
    }
}
