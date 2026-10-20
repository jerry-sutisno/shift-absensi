<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_shift', function (Blueprint $table) {
            $table->id('id_jadwal');
            $table->string('id_karyawan', 20);
            $table->date('tanggal');
            $table->string('kode_shift', 10);
            $table->enum('sumber', ['POLA', 'MANUAL', 'TUKAR_SHIFT'])->default('POLA');
            $table->enum('status', ['DRAFT', 'PUBLISHED'])->default('DRAFT');
            $table->unsignedInteger('id_pola')->nullable();
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->timestamps();

            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan')->onDelete('cascade');
            $table->foreign('kode_shift')->references('kode_shift')->on('shift')->onDelete('restrict');
            $table->foreign('id_pola')->references('id_pola')->on('pola_rotasi')->onDelete('set null');
            $table->foreign('dibuat_oleh')->references('id_user')->on('users')->onDelete('set null');

            $table->unique(['id_karyawan', 'tanggal']);
            $table->index(['tanggal', 'kode_shift']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_shift');
    }
};
