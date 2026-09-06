<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->string('nim')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_mhs');
            $table->string('kelas');
            $table->unsignedBigInteger('id_prodi');
            $table->string('nidn')->nullable();
            $table->unsignedBigInteger('id_pem')->nullable();
            
            $table->foreign('id_prodi')->references('id_prodi')->on('program_studis')->cascadeOnDelete();
            $table->foreign('nidn')->references('nidn')->on('dosens')->nullOnDelete();
            $table->foreign('id_pem')->references('id_pem')->on('pembimbing_industris')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
