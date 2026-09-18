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
            $table->string('nim');
            $table->string('kd_mat');
            $table->date('tanggal');
            $table->integer('durasi_mnt');
            $table->text('kegiatan');
            $table->string('status')->default('Kerja');
            $table->string('jam_mulai')->nullable();
            $table->string('jam_selesai')->nullable();
            $table->integer('minggu_ke')->nullable();
            
            $table->foreign('nim')->references('nim')->on('mahasiswas')->cascadeOnDelete();
            $table->foreign('kd_mat')->references('kd_mat')->on('mata_kuliahs')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
