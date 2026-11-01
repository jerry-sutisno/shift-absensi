<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * @property int $id_log
 * @property int|null $id_user
 * @property string $aksi
 * @property string $tabel
 * @property string $id_record
 * @property array<array-key, mixed>|null $data_lama
 * @property array<array-key, mixed>|null $data_baru
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereDataBaru($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereDataLama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIdLog($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIdRecord($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereTabel($value)
 * @mixin \Eloquent
 */
class AuditLog extends Model
{
    public $timestamps = false;
    protected $table = 'audit_log';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_user',
        'aksi',
        'tabel',
        'id_record',
        'data_lama',
        'data_baru',
        'ip_address',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'data_lama' => 'array',
            'data_baru' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public static function catat(
        string $aksi,
        string $tabel,
        string $idRecord,
        ?array $dataLama = null,
        ?array $dataBaru = null,
        ?int $idUser = null
    ): static {
        return static::create([
            'id_user' => $idUser ?? Auth::id(),
            'aksi' => $aksi,
            'tabel' => $tabel,
            'id_record' => (string) $idRecord,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => Request::ip(),
            'created_at' => now('Asia/Jakarta'),
        ]);
    }
}
