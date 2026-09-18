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
        Schema::table('disiplin_mahasiswas', function (Blueprint $table) {
            $table->dropColumn(['p1', 'p2', 'p3', 'p4', 'p5', 's3', 's5', 's6', 's7', 's8']);
            $table->longText('penilaian')->nullable()->after('id_pem');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disiplin_mahasiswas', function (Blueprint $table) {
            $table->dropColumn('penilaian');
            $table->float('p1')->default(0);
            $table->float('p2')->default(0);
            $table->float('p3')->default(0);
            $table->float('p4')->default(0);
            $table->float('p5')->default(0);
            $table->float('s3')->default(0);
            $table->float('s5')->default(0);
            $table->float('s6')->default(0);
            $table->float('s7')->default(0);
            $table->float('s8')->default(0);
        });
    }
};
