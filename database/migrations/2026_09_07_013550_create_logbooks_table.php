<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id('id_log');
            $table->foreignId('mahasiswa_id')->constrained()->cascadeOnDelete();
            
            // WAJIB Unsigned Big Integer karena merujuk ke $table->id()
            $table->unsignedBigInteger('id_mat');
            $table->foreign('id_mat')->references('id_mat')->on('mata_kuliahs')->cascadeOnDelete();
            
            $table->date('tanggal');
            $table->text('kegiatan');
            $table->integer('durasi_mnt');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};