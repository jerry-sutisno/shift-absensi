<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolaRotasiDetail extends Model
{
    protected $table = 'pola_rotasi_detail';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_pola',
        'id_grup',
        'hari_ke',
        'kode_shift',
    ];

    protected function casts(): array
    {
        return [
            'hari_ke' => 'integer',
        ];
    }

    public function polaRotasi(): BelongsTo
    {
        return $this->belongsTo(PolaRotasi::class, 'id_pola', 'id_pola');
    }

    public function grup(): BelongsTo
    {
        return $this->belongsTo(GrupKaryawan::class, 'id_grup', 'id_grup');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'kode_shift', 'kode_shift');
    }
}
