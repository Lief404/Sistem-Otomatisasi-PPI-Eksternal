<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_studis', function (Blueprint $table) {
            $table->id('id_prodi');
            $table->string('nama_prodi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_studis');
    }
};
