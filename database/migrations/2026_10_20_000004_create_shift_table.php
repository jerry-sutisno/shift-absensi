<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift', function (Blueprint $table) {
            $table->string('kode_shift', 10)->primary();
            $table->string('nama_shift', 50);
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->boolean('lintas_hari')->default(false);
            $table->tinyInteger('toleransi_menit')->default(10);
            $table->string('warna', 7)->nullable();
            $table->boolean('is_libur')->default(false);
            $table->boolean('is_aktif')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift');
    }
};
