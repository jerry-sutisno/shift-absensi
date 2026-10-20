<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
