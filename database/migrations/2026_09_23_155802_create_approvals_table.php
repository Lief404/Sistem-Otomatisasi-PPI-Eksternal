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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->string('mahasiswa_nim');
            $table->string('jenis_form'); // 'presentasi', 'disiplin', 'kuisioner'
            $table->string('pihak_penilai'); // 'dosen', 'mentor', 'koordinator'
            $table->unsignedBigInteger('penilai_id'); // ID user dosen/mentor
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->text('alasan_reject')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('token_verifikasi')->unique(); // Untuk URL scan QR
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
