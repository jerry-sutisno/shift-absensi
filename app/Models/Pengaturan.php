<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $kunci
 * @property string $nilai
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaturan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaturan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaturan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaturan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaturan whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaturan whereKunci($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaturan whereNilai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pengaturan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    protected $primaryKey = 'kunci';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kunci',
        'nilai',
        'keterangan',
    ];

    public static function getValue(string $kunci, mixed $default = null): mixed
    {
        $setting = static::query()->where('kunci', $kunci)->first();
        return $setting ? $setting->nilai : $default;
    }

    public static function setValue(string $kunci, mixed $nilai, ?string $keterangan = null): static
    {
        return static::query()->updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => (string) $nilai, 'keterangan' => $keterangan]
        );
    }
}
