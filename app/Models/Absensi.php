<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_absensi
 * @property int $id_jadwal
 * @property string $id_karyawan
 * @property \Illuminate\Support\Carbon $tanggal_shift
 * @property \Illuminate\Support\Carbon|null $waktu_masuk
 * @property \Illuminate\Support\Carbon|null $waktu_pulang
 * @property string|null $metode_masuk
 * @property string|null $metode_pulang
 * @property string $status
 * @property int $menit_terlambat
 * @property bool $pulang_cepat
 * @property int|null $menit_kerja
 * @property string|null $catatan
 * @property int|null $dikoreksi_oleh
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $dikoreksiOleh
 * @property-read \App\Models\JadwalShift $jadwal
 * @property-read \App\Models\Karyawan $karyawan
 * @property-read \App\Models\User|null $korektor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi visibleFor(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereDikoreksiOleh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereIdAbsensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereIdJadwal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereIdKaryawan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereMenitKerja($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereMenitTerlambat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereMetodeMasuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereMetodePulang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi wherePulangCepat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereTanggalShift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereWaktuMasuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Absensi whereWaktuPulang($value)
 * @mixin \Eloquent
 */
class Absensi extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';

    protected $fillable = [
        'id_jadwal',
        'id_karyawan',
        'tanggal_shift',
        'waktu_masuk',
        'waktu_pulang',
        'metode_masuk',
        'metode_pulang',
        'status',
        'menit_terlambat',
        'pulang_cepat',
        'menit_kerja',
        'catatan',
        'dikoreksi_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_shift' => 'date',
            'waktu_masuk' => 'datetime',
            'waktu_pulang' => 'datetime',
            'menit_terlambat' => 'integer',
            'pulang_cepat' => 'boolean',
            'menit_kerja' => 'integer',
        ];
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalShift::class, 'id_jadwal', 'id_jadwal');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function dikoreksiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikoreksi_oleh', 'id_user');
    }

    public function korektor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikoreksi_oleh', 'id_user');
    }

    public function scopeVisibleFor($query, User $user)
    {
        if ($user->isAdminHr() || $user->isDirektur()) {
            return $query;
        }

        if ($user->isHod() || $user->isSupervisor()) {
            return $query->whereHas('karyawan', function ($q) use ($user) {
                $q->where('id_departemen', $user->karyawan?->id_departemen);
            });
        }

        if ($user->isOperator()) {
            return $query->where('id_karyawan', $user->id_karyawan);
        }

        return $query->whereRaw('1 = 0');
    }
}
