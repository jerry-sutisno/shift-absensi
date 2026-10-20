<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pola_rotasi_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedInteger('id_pola');
            $table->unsignedInteger('id_grup');
            $table->tinyInteger('hari_ke');
            $table->string('kode_shift', 10);
            $table->timestamps();

            $table->foreign('id_pola')->references('id_pola')->on('pola_rotasi')->onDelete('cascade');
            $table->foreign('id_grup')->references('id_grup')->on('grup_karyawan')->onDelete('cascade');
            $table->foreign('kode_shift')->references('kode_shift')->on('shift')->onDelete('cascade');

            $table->unique(['id_pola', 'id_grup', 'hari_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pola_rotasi_detail');
    }
};
