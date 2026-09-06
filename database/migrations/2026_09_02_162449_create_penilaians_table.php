<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id('id_nilai');
            $table->string('nim');
            $table->string('nidn')->nullable();
            $table->unsignedBigInteger('id_pem')->nullable();
            $table->float('n_presentasi')->default(0);
            $table->float('n_makalah')->default(0);
            $table->float('n_prestasi')->default(0);
            $table->float('n_supervisi')->default(0);
            
            $table->foreign('nim')->references('nim')->on('mahasiswas')->cascadeOnDelete();
            $table->foreign('nidn')->references('nidn')->on('dosens')->nullOnDelete();
            $table->foreign('id_pem')->references('id_pem')->on('pembimbing_industris')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
