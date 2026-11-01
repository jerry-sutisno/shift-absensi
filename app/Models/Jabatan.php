<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_jabatan
 * @property string|null $kode_jabatan
 * @property string $nama_jabatan
 * @property int $id_departemen
 * @property string $level_jabatan
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Departemen $departemen
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Karyawan> $karyawan
 * @property-read int|null $karyawan_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereIdDepartemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereIdJabatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereKodeJabatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereLevelJabatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereNamaJabatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $primaryKey = 'id_jabatan';

    protected $fillable = [
        'kode_jabatan',
        'nama_jabatan',
        'id_departemen',
        'level_jabatan',
    ];

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id_departemen');
    }

    public function karyawan(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'id_jabatan', 'id_jabatan');
    }
}
