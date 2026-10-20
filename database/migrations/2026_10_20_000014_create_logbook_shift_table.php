<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook_shift', function (Blueprint $table) {
            $table->id('id_logbook');
            $table->date('tanggal');
            $table->string('kode_shift', 10);
            $table->unsignedInteger('id_departemen');
            $table->unsignedInteger('id_grup')->nullable();
            $table->unsignedBigInteger('id_pembuat');
            $table->text('ringkasan');
            $table->text('kendala')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->timestamps();

            $table->foreign('kode_shift')->references('kode_shift')->on('shift')->onDelete('restrict');
            $table->foreign('id_departemen')->references('id_departemen')->on('departemen')->onDelete('restrict');
            $table->foreign('id_grup')->references('id_grup')->on('grup_karyawan')->onDelete('set null');
            $table->foreign('id_pembuat')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_shift');
    }
};
