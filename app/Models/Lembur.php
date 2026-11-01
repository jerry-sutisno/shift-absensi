<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_lembur
 * @property string $id_karyawan
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $jam_mulai
 * @property string $jam_selesai
 * @property string $alasan
 * @property string $status
 * @property int|null $id_approver
 * @property int|null $menit_lembur
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\Karyawan $karyawan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereAlasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereIdApprover($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereIdKaryawan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereIdLembur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereJamMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereJamSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereMenitLembur($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lembur whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Lembur extends Model
{
    protected $table = 'lembur';
    protected $primaryKey = 'id_lembur';

    protected $fillable = [
        'id_karyawan',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'alasan',
        'status',
        'id_approver',
        'menit_lembur',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'menit_lembur' => 'integer',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_approver', 'id_user');
    }
}
