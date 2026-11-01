<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Support\Carbon $tanggal
 * @property string $keterangan
 * @property string $jenis
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HariLibur newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HariLibur newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HariLibur query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HariLibur whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HariLibur whereJenis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HariLibur whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HariLibur whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HariLibur whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class HariLibur extends Model
{
    protected $table = 'hari_libur';
    protected $primaryKey = 'tanggal';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tanggal',
        'keterangan',
        'jenis',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }
}
