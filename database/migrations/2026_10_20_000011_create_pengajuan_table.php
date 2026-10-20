<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id('id_pengajuan');
            $table->string('id_karyawan', 20);
            $table->enum('jenis', ['CUTI', 'IZIN', 'SAKIT']);
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->text('alasan');
            $table->string('lampiran_path', 255)->nullable();
            $table->enum('status', ['DIAJUKAN', 'DISETUJUI', 'DITOLAK', 'DIBATALKAN'])->default('DIAJUKAN');
            $table->enum('tahap', ['SUPERVISOR', 'HOD', 'SELESAI'])->default('SUPERVISOR');
            $table->unsignedBigInteger('id_approver')->nullable();
            $table->text('catatan_approver')->nullable();
            $table->dateTime('keputusan_at')->nullable();
            $table->timestamps();

            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawan')->onDelete('cascade');
            $table->foreign('id_approver')->references('id_user')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan');
    }
};
