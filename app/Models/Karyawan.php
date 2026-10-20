<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_karyawan',
        'nama_karyawan',
        'jns_kelamin',
        'tgl_lahir',
        'id_departemen',
        'id_jabatan',
        'id_grup',
        'status_karyawan',
        'no_hp',
    ];

    protected function casts(): array
    {
        return [
            'tgl_lahir' => 'date',
        ];
    }

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id_departemen');
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan', 'id_jabatan');
    }

    public function grup(): BelongsTo
    {
        return $this->belongsTo(GrupKaryawan::class, 'id_grup', 'id_grup');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id_karyawan', 'id_karyawan');
    }

    public function jadwalShift(): HasMany
    {
        return $this->hasMany(JadwalShift::class, 'id_karyawan', 'id_karyawan');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'id_karyawan', 'id_karyawan');
    }

    public function pengajuan(): HasMany
    {
        return $this->hasMany(Pengajuan::class, 'id_karyawan', 'id_karyawan');
    }

    public function tukarShiftPemohon(): HasMany
    {
        return $this->hasMany(TukarShift::class, 'id_pemohon', 'id_karyawan');
    }

    public function tukarShiftPengganti(): HasMany
    {
        return $this->hasMany(TukarShift::class, 'id_pengganti', 'id_karyawan');
    }

    public function lembur(): HasMany
    {
        return $this->hasMany(Lembur::class, 'id_karyawan', 'id_karyawan');
    }

    public function isAktif(): bool
    {
        return $this->status_karyawan === 'AKTIF';
    }
}
