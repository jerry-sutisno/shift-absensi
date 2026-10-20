<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
