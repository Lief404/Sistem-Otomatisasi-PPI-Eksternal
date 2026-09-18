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
        Schema::create('parameter_penilaians', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['presentasi', 'makalah']);
            $table->string('sub_kategori');
            $table->json('indikator');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameter_penilaians');
    }
};
