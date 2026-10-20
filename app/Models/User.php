<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'id_karyawan',
        'password',
        'pin_absen',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'pin_absen',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'id_user', 'id_user');
    }

    public function auditLog(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'id_user', 'id_user');
    }

    public function isAdminHr(): bool
    {
        return $this->role === 'ADMIN_HR';
    }

    public function isDirektur(): bool
    {
        return $this->role === 'DIREKTUR';
    }

    public function isHod(): bool
    {
        return $this->role === 'HOD';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'SUPERVISOR';
    }

    public function isOperator(): bool
    {
        return $this->role === 'OPERATOR';
    }

    public function getNamaAttribute(): string
    {
        return $this->karyawan?->nama_karyawan ?? $this->id_karyawan;
    }
}
