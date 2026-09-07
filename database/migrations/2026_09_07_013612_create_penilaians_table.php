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
        Schema::create('penilaians', function (Blueprint $table) {
    $table->id('id_nilai');
    $table->foreignId('mahasiswa_id')->unique()->constrained()->cascadeOnDelete(); // 1-to-1
    $table->foreignId('dosen_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('mentor_id')->nullable()->constrained()->nullOnDelete();
    
    // Nilai Dosen
    $table->float('n_presentasi')->nullable();
    $table->float('n_makalah')->nullable();
    
    // Nilai Mentor
    $table->float('n_prestasi')->nullable();
    $table->float('n_supervisi')->nullable();
    
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
