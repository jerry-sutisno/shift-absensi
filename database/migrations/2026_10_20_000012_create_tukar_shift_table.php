<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tukar_shift', function (Blueprint $table) {
            $table->id('id_tukar');
            $table->string('id_pemohon', 20);
            $table->string('id_pengganti', 20);
            $table->unsignedBigInteger('id_jadwal_pemohon');
            $table->unsignedBigInteger('id_jadwal_pengganti');
            $table->text('alasan');
            $table->enum('status', ['MENUNGGU_PENGGANTI', 'MENUNGGU_SUPERVISOR', 'DISETUJUI', 'DITOLAK', 'DIBATALKAN'])->default('MENUNGGU_PENGGANTI');
            $table->unsignedBigInteger('id_approver')->nullable();
            $table->dateTime('keputusan_at')->nullable();
            $table->timestamps();

            $table->foreign('id_pemohon')->references('id_karyawan')->on('karyawan')->onDelete('cascade');
            $table->foreign('id_pengganti')->references('id_karyawan')->on('karyawan')->onDelete('cascade');
            $table->foreign('id_jadwal_pemohon')->references('id_jadwal')->on('jadwal_shift')->onDelete('cascade');
            $table->foreign('id_jadwal_pengganti')->references('id_jadwal')->on('jadwal_shift')->onDelete('cascade');
            $table->foreign('id_approver')->references('id_user')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tukar_shift');
    }
};
