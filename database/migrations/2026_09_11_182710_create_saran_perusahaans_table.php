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
        Schema::create('saran_perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->string('perusahaan');
            $table->date('tanggal');
            $table->text('saran');
            $table->timestamps();

            $table->foreign('nim')->references('nim')->on('mahasiswas')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saran_perusahaans');
    }
};
