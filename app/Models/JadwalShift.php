<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id_jadwal
 * @property string $id_karyawan
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $kode_shift
 * @property string $sumber
 * @property string $status
 * @property int|null $id_pola
 * @property int|null $dibuat_oleh
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Absensi|null $absensi
 * @property-read \App\Models\User|null $dibuatOleh
 * @property-read \App\Models\Karyawan $karyawan
 * @property-read \App\Models\PolaRotasi|null $polaRotasi
 * @property-read \App\Models\Shift $shift
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift published()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift visibleFor(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift whereDibuatOleh($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift whereIdJadwal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift whereIdKaryawan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift whereIdPola($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift whereKodeShift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift whereSumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalShift whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class JadwalShift extends Model
{
    protected $table = 'jadwal_shift';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'id_karyawan',
        'tanggal',
        'kode_shift',
        'sumber',
        'status',
        'id_pola',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'kode_shift', 'kode_shift');
    }

    public function polaRotasi(): BelongsTo
    {
        return $this->belongsTo(PolaRotasi::class, 'id_pola', 'id_pola');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'id_user');
    }

    public function absensi(): HasOne
    {
        return $this->hasOne(Absensi::class, 'id_jadwal', 'id_jadwal');
    }

    public function isPublished(): bool
    {
        return $this->status === 'PUBLISHED';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'PUBLISHED');
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
            return $query->where('id_karyawan', $user->id_karyawan)
                ->where('status', 'PUBLISHED');
        }

        return $query->whereRaw('1 = 0');
    }
}
