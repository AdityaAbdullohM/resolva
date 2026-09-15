<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign key, alter column to allow NULL, then re-add foreign key
        Schema::table('kelas_mata_pelajaran', function (Blueprint $table) {
            $table->dropForeign(['mata_pelajaran_id']);
        });

        DB::statement('ALTER TABLE kelas_mata_pelajaran MODIFY mata_pelajaran_id BIGINT UNSIGNED NULL');

        Schema::table('kelas_mata_pelajaran', function (Blueprint $table) {
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajarans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas_mata_pelajaran', function (Blueprint $table) {
            $table->dropForeign(['mata_pelajaran_id']);
        });

        DB::statement('ALTER TABLE kelas_mata_pelajaran MODIFY mata_pelajaran_id BIGINT UNSIGNED NOT NULL');

        Schema::table('kelas_mata_pelajaran', function (Blueprint $table) {
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajarans')->onDelete('cascade');
        });
    }
};
