<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop foreign key in logbooks
        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropForeign(['kd_mat']); // default naming convention: logbooks_kd_mat_foreign
        });

        // 2. Modify mata_kuliahs
        Schema::table('mata_kuliahs', function (Blueprint $table) {
            $table->dropPrimary('kd_mat'); // Or dropPrimary(['kd_mat']) depending on laravel version
        });

        Schema::table('mata_kuliahs', function (Blueprint $table) {
            $table->id()->first();
            $table->unsignedBigInteger('id_prodi')->nullable()->after('id');
            // Change jam_min to decimal to support TRMO's 106.67
            $table->decimal('jam_min', 8, 2)->change();
            
            $table->foreign('id_prodi')->references('id_prodi')->on('program_studis')->cascadeOnDelete();
        });

        // Truncate existing generic data because it doesn't match the new structure
        DB::table('mata_kuliahs')->truncate();
    }

    public function down(): void
    {
        Schema::table('mata_kuliahs', function (Blueprint $table) {
            $table->dropForeign(['id_prodi']);
            $table->dropColumn(['id', 'id_prodi']);
            $table->integer('jam_min')->change();
            $table->primary('kd_mat');
        });

        Schema::table('logbooks', function (Blueprint $table) {
            $table->foreign('kd_mat')->references('kd_mat')->on('mata_kuliahs')->cascadeOnDelete();
        });
    }
};
