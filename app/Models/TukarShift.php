<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_tukar
 * @property string $id_pemohon
 * @property string $id_pengganti
 * @property int $id_jadwal_pemohon
 * @property int $id_jadwal_pengganti
 * @property string $alasan
 * @property string $status
 * @property int|null $id_approver
 * @property \Illuminate\Support\Carbon|null $keputusan_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\JadwalShift $jadwalPemohon
 * @property-read \App\Models\JadwalShift $jadwalPengganti
 * @property-read \App\Models\Karyawan $pemohon
 * @property-read \App\Models\Karyawan $pengganti
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift whereAlasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift whereIdApprover($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift whereIdJadwalPemohon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift whereIdJadwalPengganti($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift whereIdPemohon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift whereIdPengganti($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift whereIdTukar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift whereKeputusanAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TukarShift whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TukarShift extends Model
{
    protected $table = 'tukar_shift';
    protected $primaryKey = 'id_tukar';

    protected $fillable = [
        'id_pemohon',
        'id_pengganti',
        'id_jadwal_pemohon',
        'id_jadwal_pengganti',
        'alasan',
        'status',
        'id_approver',
        'keputusan_at',
    ];

    protected function casts(): array
    {
        return [
            'keputusan_at' => 'datetime',
        ];
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_pemohon', 'id_karyawan');
    }

    public function pengganti(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_pengganti', 'id_karyawan');
    }

    public function jadwalPemohon(): BelongsTo
    {
        return $this->belongsTo(JadwalShift::class, 'id_jadwal_pemohon', 'id_jadwal');
    }

    public function jadwalPengganti(): BelongsTo
    {
        return $this->belongsTo(JadwalShift::class, 'id_jadwal_pengganti', 'id_jadwal');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_approver', 'id_user');
    }
}
