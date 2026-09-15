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
        Schema::table('kelas', function (Blueprint $table) {
            $table->foreignId('mata_pelajaran_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            // If you want to revert, you might need to make it non-nullable again
            // and handle existing null values, or drop and re-add the column.
            // For simplicity, we'll just revert to non-nullable.
            $table->foreignId('mata_pelajaran_id')->change();
        });
    }
};