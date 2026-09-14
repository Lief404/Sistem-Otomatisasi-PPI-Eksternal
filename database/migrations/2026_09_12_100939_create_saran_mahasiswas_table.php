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
        Schema::create('saran_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->unsignedBigInteger('id_pem');
            $table->date('tanggal');
            $table->text('saran');
            $table->timestamps();

            $table->foreign('nim')->references('nim')->on('mahasiswas')->cascadeOnDelete();
            $table->foreign('id_pem')->references('id_pem')->on('pembimbing_industris')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saran_mahasiswas');
    }
};
