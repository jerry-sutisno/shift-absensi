<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jabatan', function (Blueprint $table) {
            $table->increments('id_jabatan');
            $table->string('kode_jabatan', 20)->nullable();
            $table->string('nama_jabatan', 50);
            $table->unsignedInteger('id_departemen');
            $table->enum('level_jabatan', ['DIREKTUR', 'HOD', 'SUPERVISOR', 'OPERATOR']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('id_departemen')->references('id_departemen')->on('departemen')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jabatan');
    }
};
