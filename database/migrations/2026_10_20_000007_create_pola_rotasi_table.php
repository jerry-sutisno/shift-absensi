<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pola_rotasi', function (Blueprint $table) {
            $table->increments('id_pola');
            $table->string('nama_pola', 50);
            $table->tinyInteger('panjang_siklus_hari');
            $table->text('keterangan')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pola_rotasi');
    }
};
