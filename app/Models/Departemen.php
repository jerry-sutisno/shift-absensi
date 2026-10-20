<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
