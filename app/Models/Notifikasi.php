<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id_notifikasi
 * @property int $id_user
 * @property string $judul
 * @property string $pesan
 * @property string|null $url
 * @property \Illuminate\Support\Carbon|null $dibaca_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereDibacaAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereIdNotifikasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereJudul($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi wherePesan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notifikasi whereUrl($value)
 * @mixin \Eloquent
 */
class Notifikasi extends Model
{
    public $timestamps = false;
    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';

    protected $fillable = [
        'id_user',
        'judul',
        'pesan',
        'url',
        'dibaca_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'dibaca_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
