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
        Schema::create('disiplin_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->unsignedBigInteger('id_pem');
            $table->integer('p1')->default(0);
            $table->integer('p2')->default(0);
            $table->integer('p3')->default(0);
            $table->integer('p4')->default(0);
            $table->integer('p5')->default(0);
            $table->integer('s3')->default(0);
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
        Schema::dropIfExists('disiplin_mahasiswas');
    }
};
