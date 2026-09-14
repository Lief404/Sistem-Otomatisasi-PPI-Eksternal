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
        Schema::create('kuisioner_mentors', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->unsignedBigInteger('id_pem');
            
            // Hard Skills
            $table->integer('h1a')->default(0);
            $table->integer('h1b')->default(0);
            $table->integer('h1c')->default(0);
            $table->integer('h1d')->default(0);
            $table->integer('h2')->default(0);
            $table->integer('h3')->default(0);
            $table->integer('h4')->default(0);

            // Soft Skills
            $table->integer('s1')->default(0);
            $table->integer('s2')->default(0);
            $table->integer('s3')->default(0);
            $table->integer('s4')->default(0);
            $table->integer('s5')->default(0);
            $table->integer('s6')->default(0);
            $table->integer('s7')->default(0);
            $table->integer('s8')->default(0);
            
            $table->date('tanggal');

            $table->foreign('nim')->references('nim')->on('mahasiswas')->cascadeOnDelete();
            $table->foreign('id_pem')->references('id_pem')->on('pembimbing_industris')->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuisioner_mentors');
    }
};
