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
        Schema::create('mahasiswas', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('nim')->unique();
    $table->enum('prodi', ['TRO', 'TRMO', 'TRIN']);
    $table->enum('kelas', [
        'AEB1', 'AEB2', 'AEB3', 'AEB4', // TRO
        'AEA1', 'AEA2', 'AEA3', 'AEA4', // TRMO
        'AEC1', 'AEC2', 'AEC3', 'AEC4'  // TRIN
    ]);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
