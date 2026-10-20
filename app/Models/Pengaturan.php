<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
