<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->string('id_karyawan', 20)->primary();
            $table->string('nama_karyawan', 50);
            $table->enum('jns_kelamin', ['L', 'P']);
            $table->date('tgl_lahir');
            $table->unsignedInteger('id_departemen');
            $table->unsignedInteger('id_jabatan');
            $table->unsignedInteger('id_grup')->nullable();
            $table->enum('status_karyawan', ['AKTIF', 'NONAKTIF'])->default('AKTIF');
            $table->string('no_hp', 20)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('id_departemen')->references('id_departemen')->on('departemen')->onDelete('restrict');
            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatan')->onDelete('restrict');
            $table->foreign('id_grup')->references('id_grup')->on('grup_karyawan')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
