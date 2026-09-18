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
        Schema::table('kuisioner_mentors', function (Blueprint $table) {
            $table->dropColumn(['h1a', 'h1b', 'h1c', 'h1d', 'h2', 'h3', 'h4', 's1', 's2', 's3', 's4', 's5', 's6', 's7', 's8']);
            $table->longText('penilaian')->nullable()->after('id_pem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kuisioner_mentors', function (Blueprint $table) {
            $table->dropColumn('penilaian');
            $table->float('h1a')->default(0);
            $table->float('h1b')->default(0);
            $table->float('h1c')->default(0);
            $table->float('h1d')->default(0);
            $table->float('h2')->default(0);
            $table->float('h3')->default(0);
            $table->float('h4')->default(0);
            $table->float('s1')->default(0);
            $table->float('s2')->default(0);
            $table->float('s3')->default(0);
            $table->float('s4')->default(0);
            $table->float('s5')->default(0);
            $table->float('s6')->default(0);
            $table->float('s7')->default(0);
            $table->float('s8')->default(0);
        });
    }
};
