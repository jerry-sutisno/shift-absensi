<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_pengajuan
 * @property string $id_karyawan
 * @property string $jenis
 * @property \Illuminate\Support\Carbon $tgl_mulai
 * @property \Illuminate\Support\Carbon $tgl_selesai
 * @property string $alasan
 * @property string|null $lampiran_path
 * @property string $status
 * @property string $tahap
 * @property int|null $id_approver
 * @property string|null $catatan_approver
 * @property \Illuminate\Support\Carbon|null $keputusan_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $approver
 * @property-read \App\Models\Karyawan $karyawan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereAlasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereCatatanApprover($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereIdApprover($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereIdKaryawan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereIdPengajuan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereKeputusanAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereLampiranPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereTahap($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereTglMulai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereTglSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengajuan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Pengajuan extends Model
{
    protected $table = 'pengajuan';
    protected $primaryKey = 'id_pengajuan';

    protected $fillable = [
        'id_karyawan',
        'jenis',
        'tgl_mulai',
        'tgl_selesai',
        'alasan',
        'lampiran_path',
        'status',
        'tahap',
        'id_approver',
        'catatan_approver',
        'keputusan_at',
    ];

    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_selesai' => 'date',
            'keputusan_at' => 'datetime',
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
