<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

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
