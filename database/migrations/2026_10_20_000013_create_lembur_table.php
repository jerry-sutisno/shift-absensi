<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lembur', function (Blueprint $table) {
            $table->id('id_lembur');
            $table->string('id_karyawan', 20);
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->text('alasan');
            $table->enum('status', ['DIAJUKAN', 'DISETUJUI', 'DITOLAK'])->default('DIAJUKAN');
            $table->unsignedBigInteger('id_approver')->nullable();
            $table->smallInteger('menit_lembur')->nullable();
            $table->timestamps();

            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan')->onDelete('cascade');
            $table->foreign('id_approver')->references('id_user')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembur');
    }
};
