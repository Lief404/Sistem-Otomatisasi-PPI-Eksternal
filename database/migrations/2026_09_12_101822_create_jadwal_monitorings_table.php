<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal_monitorings', function (Blueprint $table) {
            $table->id();
            $table->string('nidn');
            $table->string('perusahaan');
            $table->date('tanggal');
            
            $table->foreign('nidn')->references('nidn')->on('dosens')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_monitorings');
    }
};
