<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id_karyawan
 * @property string $nama_karyawan
 * @property string $jns_kelamin
 * @property \Illuminate\Support\Carbon $tgl_lahir
 * @property int $id_departemen
 * @property int $id_jabatan
 * @property int|null $id_grup
 * @property string $status_karyawan
 * @property string|null $no_hp
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Absensi> $absensi
 * @property-read int|null $absensi_count
 * @property-read \App\Models\Departemen $departemen
 * @property-read \App\Models\GrupKaryawan|null $grup
 * @property-read \App\Models\Jabatan $jabatan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JadwalShift> $jadwalShift
 * @property-read int|null $jadwal_shift_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lembur> $lembur
 * @property-read int|null $lembur_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pengajuan> $pengajuan
 * @property-read int|null $pengajuan_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TukarShift> $tukarShiftPemohon
 * @property-read int|null $tukar_shift_pemohon_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TukarShift> $tukarShiftPengganti
 * @property-read int|null $tukar_shift_pengganti_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan whereIdDepartemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan whereIdGrup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan whereIdJabatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan whereIdKaryawan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan whereJnsKelamin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan whereNamaKaryawan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan whereStatusKaryawan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan whereTglLahir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Karyawan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
