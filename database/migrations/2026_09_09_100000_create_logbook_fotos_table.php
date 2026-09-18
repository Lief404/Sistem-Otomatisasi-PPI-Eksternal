<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel baru untuk menyimpan banyak foto per hari per mahasiswa
        Schema::create('logbook_fotos', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->date('tanggal');
            $table->string('foto_path');
            $table->timestamps();

            $table->foreign('nim')->references('nim')->on('mahasiswas')->cascadeOnDelete();
        });

        // Hapus kolom foto lama dari logbooks (sudah tidak dipakai)
        if (Schema::hasColumn('logbooks', 'foto')) {
            Schema::table('logbooks', function (Blueprint $table) {
                $table->dropColumn('foto');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_fotos');

        Schema::table('logbooks', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('minggu_ke');
        });
    }
};
