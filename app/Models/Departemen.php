<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id_departemen
 * @property string $nama_departemen
 * @property string $kode_departemen
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Jabatan> $jabatan
 * @property-read int|null $jabatan_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Karyawan> $karyawan
 * @property-read int|null $karyawan_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereIdDepartemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereKodeDepartemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereNamaDepartemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Departemen whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Departemen extends Model
{
    protected $table = 'departemen';
    protected $primaryKey = 'id_departemen';

    protected $fillable = [
        'nama_departemen',
        'kode_departemen',
        'deskripsi',
    ];

    public function jabatan(): HasMany
    {
        return $this->hasMany(Jabatan::class, 'id_departemen', 'id_departemen');
    }

    public function karyawan(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'id_departemen', 'id_departemen');
    }
}
