<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_logbook
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $kode_shift
 * @property int $id_departemen
 * @property int|null $id_grup
 * @property int $id_pembuat
 * @property string $ringkasan
 * @property string|null $kendala
 * @property string|null $tindak_lanjut
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Departemen $departemen
 * @property-read \App\Models\GrupKaryawan|null $grup
 * @property-read \App\Models\User $pembuat
 * @property-read \App\Models\Shift $shift
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift visibleFor(\App\Models\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift whereIdDepartemen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift whereIdGrup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift whereIdLogbook($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift whereIdPembuat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift whereKendala($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift whereKodeShift($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift whereRingkasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift whereTindakLanjut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LogbookShift whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LogbookShift extends Model
{
    protected $table = 'logbook_shift';
    protected $primaryKey = 'id_logbook';

    protected $fillable = [
        'tanggal',
        'kode_shift',
        'id_departemen',
        'id_grup',
        'id_pembuat',
        'ringkasan',
        'kendala',
        'tindak_lanjut',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'kode_shift', 'kode_shift');
    }

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'id_departemen', 'id_departemen');
    }

    public function grup(): BelongsTo
    {
        return $this->belongsTo(GrupKaryawan::class, 'id_grup', 'id_grup');
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pembuat', 'id_user');
    }

    public function scopeVisibleFor($query, User $user)
    {
        if ($user->isAdminHr() || $user->isDirektur()) {
            return $query;
        }

        $userDept = $user->karyawan?->id_departemen;
        if ($userDept) {
            return $query->where('id_departemen', $userDept);
        }

        return $query->whereRaw('1 = 0');
    }
}
