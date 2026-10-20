<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id('id_absensi');
            $table->unsignedBigInteger('id_jadwal')->unique();
            $table->string('id_karyawan', 20);
            $table->date('tanggal_shift');
            $table->dateTime('waktu_masuk')->nullable();
            $table->dateTime('waktu_pulang')->nullable();
            $table->enum('metode_masuk', ['KIOSK', 'MOBILE', 'MANUAL'])->nullable();
            $table->enum('metode_pulang', ['KIOSK', 'MOBILE', 'MANUAL'])->nullable();
            $table->enum('status', ['HADIR', 'TERLAMBAT', 'ALPA', 'IZIN', 'SAKIT', 'CUTI', 'LIBUR']);
            $table->smallInteger('menit_terlambat')->default(0);
            $table->boolean('pulang_cepat')->default(false);
            $table->smallInteger('menit_kerja')->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('dikoreksi_oleh')->nullable();
            $table->timestamps();

            $table->foreign('id_jadwal')->references('id_jadwal')->on('jadwal_shift')->onDelete('cascade');
            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan')->onDelete('cascade');
            $table->foreign('dikoreksi_oleh')->references('id_user')->on('users')->onDelete('set null');

            $table->unique(['id_karyawan', 'tanggal_shift']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
