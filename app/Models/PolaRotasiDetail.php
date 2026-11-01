<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_detail
 * @property int $id_pola
 * @property int $id_grup
 * @property int $hari_ke
 * @property string $kode_shift
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GrupKaryawan $grup
 * @property-read \App\Models\PolaRotasi $polaRotasi
 * @property-read \App\Models\Shift $shift
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasiDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasiDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasiDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasiDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasiDetail whereHariKe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasiDetail whereIdDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasiDetail whereIdGrup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasiDetail whereIdPola($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasiDetail whereKodeShift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PolaRotasiDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PolaRotasiDetail extends Model
{
    protected $table = 'pola_rotasi_detail';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_pola',
        'id_grup',
        'hari_ke',
        'kode_shift',
    ];

    protected function casts(): array
    {
        return [
            'hari_ke' => 'integer',
        ];
    }

    public function polaRotasi(): BelongsTo
    {
        return $this->belongsTo(PolaRotasi::class, 'id_pola', 'id_pola');
    }

    public function grup(): BelongsTo
    {
        return $this->belongsTo(GrupKaryawan::class, 'id_grup', 'id_grup');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'kode_shift', 'kode_shift');
    }
}
