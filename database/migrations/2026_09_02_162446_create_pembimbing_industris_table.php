<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembimbing_industris', function (Blueprint $table) {
            $table->id('id_pem');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_pem');
            $table->string('perusahaan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembimbing_industris');
    }
};
